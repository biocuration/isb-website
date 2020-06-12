<?php

use FernleafSystems\Wordpress\Plugin\Shield;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\SecurityAdmin;
use FernleafSystems\Wordpress\Services\Services;

class ICWP_WPSF_FeatureHandler_AdminAccessRestriction extends ICWP_WPSF_FeatureHandler_BaseWpsf {

	const HASH_DELETE = '32f68a60cef40faedbc6af20298c1a1e';

	/**
	 * @var bool
	 */
	private $bValidSecAdminRequest;

	/**
	 */
	protected function setupCustomHooks() {
		parent::setupCustomHooks();
		add_action( $this->prefix( 'pre_deactivate_plugin' ), [ $this, 'preDeactivatePlugin' ] );
	}

	/**
	 * @return bool
	 * @throws \Exception
	 */
	protected function isReadyToExecute() {
		return $this->isEnabledSecurityAdmin() && parent::isReadyToExecute();
	}

	/**
	 * @return array
	 */
	public function getSecurityAdminUsers() {
		$aU = $this->getOpt( 'sec_admin_users', [] );
		return ( is_array( $aU ) && $this->isPremium() ) ? $aU : [];
	}

	/**
	 * @return bool
	 */
	public function hasSecAdminUsers() {
		return count( $this->getSecurityAdminUsers() ) > 0;
	}

	/**
	 * No checking of admin capabilities in-case of infinite loop with admin access caps check
	 * @return bool
	 */
	public function isRegisteredSecAdminUser() {
		$sUser = Services::WpUsers()->getCurrentWpUsername();
		return !empty( $sUser ) && in_array( $sUser, $this->getSecurityAdminUsers() );
	}

	/**
	 */
	protected function preProcessOptions() {
		if ( $this->isValidSecAdminRequest() ) {
			$this->setSecurityAdminStatusOnOff( true );
		}

		// Verify whitelabel images
		if ( $this->isWlEnabled() ) {
			$aImages = [
				'wl_menuiconurl',
				'wl_dashboardlogourl',
				'wl_login2fa_logourl',
			];
			$oOpts = $this->getOptions();
			foreach ( $aImages as $sKey ) {
				if ( !Services::Data()->isValidWebUrl( $this->buildWlImageUrl( $sKey ) ) ) {
					$oOpts->resetOptToDefault( $sKey );
				}
			}
		}

		$this->setOpt( 'sec_admin_users', $this->verifySecAdminUsers( $this->getSecurityAdminUsers() ) );
	}

	/**
	 * Ensures that all entries are valid users.
	 * @param string[] $aSecUsers
	 * @return string[]
	 */
	private function verifySecAdminUsers( $aSecUsers ) {
		$oDP = Services::Data();
		$oWpUsers = Services::WpUsers();
		/** @var SecurityAdmin\Options $oOpts */
		$oOpts = $this->getOptions();

		$aFiltered = [];
		foreach ( $aSecUsers as $nCurrentKey => $sUsernameOrEmail ) {
			if ( $oDP->validEmail( $sUsernameOrEmail ) ) {
				$oUser = $oWpUsers->getUserByEmail( $sUsernameOrEmail );
			}
			else {
				$oUser = $oWpUsers->getUserByUsername( $sUsernameOrEmail );
				if ( is_null( $oUser ) && is_numeric( $sUsernameOrEmail ) ) {
					$oUser = $oWpUsers->getUserById( $sUsernameOrEmail );
				}
			}

			if ( $oUser instanceof WP_User && $oUser->ID > 0 && $oWpUsers->isUserAdmin( $oUser ) ) {
				$aFiltered[] = $oUser->user_login;
			}
		}

		// We now run a bit of a sanity check to ensure that the current user is
		// not adding users here that aren't themselves without a key to still gain access
		$oCurrent = $oWpUsers->getCurrentWpUser();
		if ( !empty( $aFiltered ) && !$oOpts->hasAccessKey() && !in_array( $oCurrent->user_login, $aFiltered ) ) {
			$aFiltered[] = $oCurrent->user_login;
		}

		natsort( $aFiltered );
		return array_unique( $aFiltered );
	}

	/**
	 * @return int
	 */
	public function getSecAdminTimeout() {
		return (int)$this->getOpt( 'admin_access_timeout' )*MINUTE_IN_SECONDS;
	}

	/**
	 * Only returns greater than 0 if you have a valid Sec admin session
	 * @return int
	 */
	public function getSecAdminTimeLeft() {
		$nLeft = 0;
		if ( $this->hasSession() ) {

			$nSecAdminAt = $this->getSession()->getSecAdminAt();
			if ( $this->isRegisteredSecAdminUser() ) {
				$nLeft = 0;
			}
			elseif ( $nSecAdminAt > 0 ) {
				$nLeft = $this->getSecAdminTimeout() - ( Services::Request()->ts() - $nSecAdminAt );
			}
		}
		return max( 0, $nLeft );
	}

	/**
	 * @inheritDoc
	 */
	protected function handleModAction( $sAction ) {
		switch ( $sAction ) {
			case  'remove_secadmin_confirm':
				( new SecurityAdmin\Lib\Actions\RemoveSecAdmin() )
					->setMod( $this )
					->remove();
				break;
			default:
				break;
		}
	}

	/**
	 * @return bool
	 */
	public function isSecAdminSessionValid() {
		return ( $this->getSecAdminTimeLeft() > 0 );
	}

	/**
	 * @return bool
	 */
	public function isEnabledSecurityAdmin() {
		/** @var SecurityAdmin\Options $oOpts */
		$oOpts = $this->getOptions();
		return $this->isModOptEnabled() &&
			   ( $this->hasSecAdminUsers() ||
				 ( $oOpts->hasAccessKey() && $this->getSecAdminTimeout() > 0 )
			   );
	}

	/**
	 * @param bool $bSetOn
	 * @return bool
	 */
	public function setSecurityAdminStatusOnOff( $bSetOn = false ) {
		/** @var Shield\Databases\Session\Update $oUpdater */
		$oUpdater = $this->getDbHandler_Sessions()->getQueryUpdater();
		return $bSetOn ?
			$oUpdater->startSecurityAdmin( $this->getSession() )
			: $oUpdater->terminateSecurityAdmin( $this->getSession() );
	}

	/**
	 * @return bool
	 */
	public function isValidSecAdminRequest() {
		return $this->isAccessKeyRequest() && $this->testSecAccessKeyRequest();
	}

	/**
	 * @return bool
	 */
	public function testSecAccessKeyRequest() {
		if ( !isset( $this->bValidSecAdminRequest ) ) {
			$bValid = false;
			$sReqKey = Services::Request()->post( 'sec_admin_key' );
			if ( !empty( $sReqKey ) ) {
				/** @var SecurityAdmin\Options $oOpts */
				$oOpts = $this->getOptions();
				$bValid = hash_equals( $oOpts->getAccessKeyHash(), md5( $sReqKey ) );
				if ( !$bValid ) {
					$sEscaped = isset( $_POST[ 'sec_admin_key' ] ) ? $_POST[ 'sec_admin_key' ] : '';
					if ( !empty( $sEscaped ) ) {
						// Workaround for escaping of passwords
						$bValid = hash_equals( $oOpts->getAccessKeyHash(), md5( $sEscaped ) );
						if ( $bValid ) {
							$this->setOpt( 'admin_access_key', md5( $sReqKey ) );
						}
					}
				}

				$this->getCon()->fireEvent( $bValid ? 'key_success' : 'key_fail' );
			}

			$this->bValidSecAdminRequest = $bValid;
		}
		return $this->bValidSecAdminRequest;
	}

	/**
	 * @return bool
	 */
	private function isAccessKeyRequest() {
		return strlen( Services::Request()->post( 'sec_admin_key', '' ) ) > 0;
	}

	/**
	 * @param string $sKey
	 * @return bool
	 */
	public function verifyAccessKey( $sKey ) {
		/** @var SecurityAdmin\Options $oOpts */
		$oOpts = $this->getOptions();
		return !empty( $sKey ) && hash_equals( $oOpts->getAccessKeyHash(), md5( $sKey ) );
	}

	/**
	 * @return array
	 */
	public function getWhitelabelOptions() {
		$sMain = $this->getOpt( 'wl_pluginnamemain' );
		$sMenu = $this->getOpt( 'wl_namemenu' );
		if ( empty( $sMenu ) ) {
			$sMenu = $sMain;
		}

		return [
			'name_main'            => $sMain,
			'name_menu'            => $sMenu,
			'name_company'         => $this->getOpt( 'wl_companyname' ),
			'description'          => $this->getOpt( 'wl_description' ),
			'url_home'             => $this->getOpt( 'wl_homeurl' ),
			'url_icon'             => $this->buildWlImageUrl( 'wl_menuiconurl' ),
			'url_dashboardlogourl' => $this->buildWlImageUrl( 'wl_dashboardlogourl' ),
			'url_login2fa_logourl' => $this->buildWlImageUrl( 'wl_login2fa_logourl' ),
		];
	}

	/**
	 * We cater for 3 options:
	 * Full URL
	 * Relative path URL: i.e. starts with /
	 * Or Plugin image URL i.e. doesn't start with HTTP or /
	 * @param string $sKey
	 * @return string
	 */
	private function buildWlImageUrl( $sKey ) {
		$oOpts = $this->getOptions();

		$sLogoUrl = $this->getOpt( $sKey );
		if ( empty( $sLogoUrl ) ) {
			$oOpts->resetOptToDefault( $sKey );
			$sLogoUrl = $this->getOpt( $sKey );
		}
		if ( !empty( $sLogoUrl ) && !Services::Data()->isValidWebUrl( $sLogoUrl ) && strpos( $sLogoUrl, '/' ) !== 0 ) {
			$sLogoUrl = $this->getCon()->getPluginUrl_Image( $sLogoUrl );
			if ( empty( $sLogoUrl ) ) {
				$oOpts->resetOptToDefault( $sKey );
				$sLogoUrl = $this->getCon()->getPluginUrl_Image( $this->getOpt( $sKey ) );
			}
		}

		return $sLogoUrl;
	}

	/**
	 * @return bool
	 */
	public function isWlEnabled() {
		/** @var SecurityAdmin\Options $oOpts */
		$oOpts = $this->getOptions();
		return $oOpts->isEnabledWhitelabel() && $this->isPremium();
	}

	/**
	 * @return bool
	 */
	public function isWlHideUpdates() {
		return $this->isWlEnabled() && $this->isOpt( 'wl_hide_updates', 'Y' );
	}

	/**
	 * @param string $sKey
	 * @return $this
	 * @throws \Exception
	 */
	public function setNewAccessKeyManually( $sKey ) {
		if ( empty( $sKey ) ) {
			throw new \Exception( 'Attempting to set an empty Security Admin Access Key.' );
		}
		if ( !$this->getCon()->isPluginAdmin() ) {
			throw new \Exception( 'User does not have permission to update the Security Admin Access Key.' );
		}

		$this->setIsMainFeatureEnabled( true )
			 ->setOpt( 'admin_access_key', md5( $sKey ) );
		return $this->saveModOptions();
	}

	public function insertCustomJsVars_Admin() {
		parent::insertCustomJsVars_Admin();

		if ( $this->getSecAdminTimeLeft() > 0 ) {
			$aInsertData = [
				'ajax'         => [
					'check' => $this->getSecAdminCheckAjaxData(),
				],
				'is_sec_admin' => true, // if $nSecTimeLeft > 0
				'timeleft'     => $this->getSecAdminTimeLeft(), // JS uses milliseconds
				'strings'      => [
					'confirm' => __( 'Security Admin session has timed-out.', 'wp-simple-firewall' ).' '.__( 'Reload now?', 'wp-simple-firewall' ),
					'nearly'  => __( 'Security Admin session has nearly timed-out.', 'wp-simple-firewall' ),
					'expired' => __( 'Security Admin session has timed-out.', 'wp-simple-firewall' )
				]
			];
		}
		else {
			$aInsertData = [
				'ajax'    => [
					'req_email_remove' => $this->getAjaxActionData( 'req_email_remove' ),
				],
				'strings' => [
					'are_you_sure' => __( 'Are you sure?', 'wp-simple-firewall' )
				]
			];
		}

		if ( !empty( $aInsertData ) ) {
			wp_localize_script(
				$this->prefix( 'plugin' ),
				'icwp_wpsf_vars_secadmin',
				$aInsertData
			);
		}
	}

	/**
	 * @param array $aAllData
	 * @return array
	 */
	public function addInsightsConfigData( $aAllData ) {
		/** @var SecurityAdmin\Options $oOpts */
		$oOpts = $this->getOptions();

		$aThis = [
			'strings'      => [
				'title' => __( 'Security Admin', 'wp-simple-firewall' ),
				'sub'   => sprintf( __( 'Prevent Tampering With %s Settings', 'wp-simple-firewall' ), $this->getCon()
																										   ->getHumanName() ),
			],
			'key_opts'     => [],
			'href_options' => $this->getUrl_AdminPage()
		];

		if ( !$this->isEnabledForUiSummary() ) {
			$aThis[ 'key_opts' ][ 'mod' ] = $this->getModDisabledInsight();
		}
		else {
			$aThis[ 'key_opts' ][ 'mod' ] = [
				'name'    => __( 'Security Admin', 'wp-simple-firewall' ),
				'enabled' => $this->isEnabledForUiSummary(),
				'summary' => $this->isEnabledForUiSummary() ?
					__( 'Security plugin is protected against tampering', 'wp-simple-firewall' )
					: __( 'Security plugin is vulnerable to tampering', 'wp-simple-firewall' ),
				'weight'  => 2,
				'href'    => $this->getUrl_DirectLinkToOption( 'admin_access_key' ),
			];

			$bWpOpts = $oOpts->getAdminAccessArea_Options();
			$aThis[ 'key_opts' ][ 'wpopts' ] = [
				'name'    => __( 'Important Options', 'wp-simple-firewall' ),
				'enabled' => $bWpOpts,
				'summary' => $bWpOpts ?
					__( 'Important WP options are protected against tampering', 'wp-simple-firewall' )
					: __( "Important WP options aren't protected against tampering", 'wp-simple-firewall' ),
				'weight'  => 2,
				'href'    => $this->getUrl_DirectLinkToOption( 'admin_access_restrict_options' ),
			];

			$bUsers = $oOpts->isSecAdminRestrictUsersEnabled();
			$aThis[ 'key_opts' ][ 'adminusers' ] = [
				'name'    => __( 'WP Admins', 'wp-simple-firewall' ),
				'enabled' => $bUsers,
				'summary' => $bUsers ?
					__( 'Admin users are protected against tampering', 'wp-simple-firewall' )
					: __( "Admin users aren't protected against tampering", 'wp-simple-firewall' ),
				'weight'  => 1,
				'href'    => $this->getUrl_DirectLinkToOption( 'admin_access_restrict_admin_users' ),
			];
		}

		$aAllData[ $this->getSlug() ] = $aThis;
		return $aAllData;
	}

	/**
	 * @param array $aAllNotices
	 * @return array
	 */
	public function addInsightsNoticeData( $aAllNotices ) {

		$aNotices = [
			'title'    => __( 'Security Admin Protection', 'wp-simple-firewall' ),
			'messages' => []
		];

		{//sec admin
			if ( !$this->isEnabledSecurityAdmin() ) {
				$aNotices[ 'messages' ][ 'sec_admin' ] = [
					'title'   => __( 'Security Plugin Unprotected', 'wp-simple-firewall' ),
					'message' => sprintf(
						__( "The Security Admin protection is not active.", 'wp-simple-firewall' ),
						$this->getCon()->getHumanName()
					),
					'href'    => $this->getUrl_AdminPage(),
					'action'  => sprintf( __( 'Go To %s', 'wp-simple-firewall' ), __( 'Options' ) ),
					'rec'     => __( 'Security Admin should be turned-on to protect your security settings.', 'wp-simple-firewall' )
				];
			}
		}

		$aNotices[ 'count' ] = count( $aNotices[ 'messages' ] );
		$aAllNotices[ 'sec_admin' ] = $aNotices;

		return $aAllNotices;
	}

	/**
	 * @return bool
	 */
	protected function isEnabledForUiSummary() {
		return parent::isEnabledForUiSummary() && $this->isEnabledSecurityAdmin();
	}

	/**
	 * This is the point where you would want to do any options verification
	 */
	protected function doPrePluginOptionsSave() {
		/** @var SecurityAdmin\Options $oOpts */
		$oOpts = $this->getOptions();

		if ( hash_equals( $oOpts->getAccessKeyHash(), self::HASH_DELETE ) ) {
			$oOpts->clearSecurityAdminKey();
			$this->setSecurityAdminStatusOnOff( false );
		}

		// Restricting Activate Plugins also means restricting the rest.
		$aPluginsRestrictions = $oOpts->getAdminAccessArea_Plugins();
		if ( in_array( 'activate_plugins', $aPluginsRestrictions ) ) {
			$oOpts->setOpt(
				'admin_access_restrict_plugins',
				array_unique( array_merge( $aPluginsRestrictions, [
					'install_plugins',
					'update_plugins',
					'delete_plugins'
				] ) )
			);
		}

		// Restricting Switch (Activate) Themes also means restricting the rest.
		$aThemesRestrictions = $oOpts->getAdminAccessArea_Themes();
		if ( in_array( 'switch_themes', $aThemesRestrictions ) && in_array( 'edit_theme_options', $aThemesRestrictions ) ) {
			$oOpts->setOpt(
				'admin_access_restrict_themes',
				array_unique( array_merge( $aThemesRestrictions, [
					'install_themes',
					'update_themes',
					'delete_themes'
				] ) )
			);
		}

		$aPostRestrictions = $oOpts->getAdminAccessArea_Posts();
		if ( in_array( 'edit', $aPostRestrictions ) ) {
			$oOpts->setOpt(
				'admin_access_restrict_posts',
				array_unique( array_merge( $aPostRestrictions, [ 'create', 'publish', 'delete' ] ) )
			);
		}
	}

	/**
	 */
	public function preDeactivatePlugin() {
		if ( !$this->getCon()->isPluginAdmin() ) {
			Services::WpGeneral()->wpDie(
				__( "Sorry, this plugin is protected against unauthorised attempts to disable it.", 'wp-simple-firewall' )
				.'<br />'.sprintf( '<a href="%s">%s</a>',
					$this->getUrl_AdminPage(),
					__( "You'll just need to authenticate first and try again.", 'wp-simple-firewall' )
				)
			);
		}
	}

	/**
	 * @return string
	 */
	protected function getNamespaceBase() {
		return 'SecurityAdmin';
	}
}