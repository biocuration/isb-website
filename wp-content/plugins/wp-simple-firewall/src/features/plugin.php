<?php

if ( class_exists( 'ICWP_WPSF_FeatureHandler_Plugin', false ) ) {
	return;
}

require_once( dirname( __FILE__ ).DIRECTORY_SEPARATOR.'base_wpsf.php' );

class ICWP_WPSF_FeatureHandler_Plugin extends ICWP_WPSF_FeatureHandler_BaseWpsf {

	protected function doPostConstruction() {
		add_action( 'deactivate_plugin', array( $this, 'onWpHookDeactivatePlugin' ), 1, 1 );
		add_filter( $this->prefix( 'report_email_address' ), array( $this, 'getPluginReportEmail' ) );
		add_filter( $this->prefix( 'globally_disabled' ), array( $this, 'filter_IsPluginGloballyDisabled' ) );
		add_filter( $this->prefix( 'google_recaptcha_config' ), array( $this, 'supplyGoogleRecaptchaConfig' ), 10, 0 );

		if ( !$this->isTrackingPermissionSet() ) {
			add_action( 'wp_ajax_icwp_PluginTrackingPermission', array( $this, 'ajaxSetPluginTrackingPermission' ) );
		}
		$this->setVisitorIp();
	}

	/**
	 * A action added to WordPress 'init' hook
	 */
	public function onWpInit() {
		parent::onWpInit();
		$this->getImportExportSecretKey();
	}

	/**
	 * @return string
	 */
	protected function getContentCustomActions() {
		if ( !$this->canDisplayOptionsForm() ) {
			return parent::getContentCustomActions();
		}

		$bCanWizard = $this->canRunWizards();
		$bCanWizardWelcome = $bCanWizard;
		$bCanWizardImport = $bCanWizard && $this->isPremium();

		$aData = array(
			'strings' => $this->getDisplayStrings(),
			'hrefs'   => array(
				'wizard_welcome' => $bCanWizardWelcome ? $this->getUrl_Wizard( 'welcome' ) : 'javascript:{event.preventDefault();}',
				'wizard_import'  => $bCanWizardImport ? $this->getUrl_Wizard( 'import' ) : 'javascript:{event.preventDefault();}',
			),
			'flags'   => array(
				'can_php54'   => $bCanWizard,
				'can_welcome' => $bCanWizardWelcome,
				'can_import'  => $bCanWizardImport
			),
			'data'    => array(
				'phpversion' => $this->loadDP()->getPhpVersion(),
			)
		);
		return $this->renderTemplate( 'snippets/module-plugin-actions', $aData );
	}

	/**
	 * @return ICWP_WPSF_Processor_Plugin_ImportExport
	 */
	protected function getImportProcessor() {
		/** @var ICWP_WPSF_Processor_Plugin $oP */
		$oP = $this->getProcessor();
		return $oP->getSubProcessorImportExport();
	}

	/**
	 * @return bool
	 */
	public function getLastCheckServerIpAtHasExpired() {
		return ( ( $this->loadDataProcessor()->time() - $this->getLastCheckServerIpAt() ) > DAY_IN_SECONDS );
	}

	/**
	 * @return int
	 */
	public function getLastCheckServerIpAt() {
		return $this->getOpt( 'this_server_ip_last_check_at', 0 );
	}

	/**
	 * @return string
	 */
	public function getMyServerIp() {

		$sThisServerIp = $this->getOpt( 'this_server_ip', '' );
		if ( $this->getLastCheckServerIpAtHasExpired() ) {
			$oIp = $this->loadIpService();
			$sThisServerIp = $oIp->whatIsMyIp();
			if ( $oIp->isValidIp_PublicRemote( $sThisServerIp ) ) {
				$this->setOpt( 'this_server_ip', $sThisServerIp );
			}
			// we always update so we don't forever check on every single page load
			$this->setOpt( 'this_server_ip_last_check_at', $this->loadDataProcessor()->time() );
		}
		return $sThisServerIp;
	}

	/**
	 * @return bool
	 */
	public function isDisplayPluginBadge() {
		return $this->getOptIs( 'display_plugin_badge', 'Y' )
			   && ( $this->loadDataProcessor()->FetchCookie( $this->getCookieIdBadgeState() ) != 'closed' );
	}

	/**
	 * @param bool $bDisplay
	 * @return $this
	 */
	public function setIsDisplayPluginBadge( $bDisplay ) {
		return $this->setOpt( 'display_plugin_badge', $bDisplay ? 'Y' : 'N' );
	}

	/**
	 * Forcefully sets the Visitor IP address in the Data component for use throughout the plugin
	 */
	protected function setVisitorIp() {
		$sIp = null;
		$oIpService = $this->loadIpService();
		$oDp = $this->loadDP();

		if ( !$this->isVisitorAddressSourceAutoDetect() ) {

			$sIp = $oDp->FetchServer( $this->getVisitorAddressSource() );
			if ( $oIpService->isViablePublicVisitorIp( $sIp, $this->getMyServerIp() ) ) {
				$oIpService->setRequestIpAddress( $sIp );
			}
			else {
				$sIp = null;
			}
		}

		// If the address at this stage is null, then the current setting is failing for IP detection
		// So we try and rediscover a more correct source for the Request IP Address.
		if ( empty( $sIp ) ) {
			$sSource = $oIpService->setServerIpAddress( $this->getMyServerIp() )
								  ->discoverViableRequestIpSource();
			if ( !empty( $sSource ) ) {
				$oIpService->setRequestIpAddress( $oDp->FetchServer( $sSource ) );
				$this->setVisitorAddressSource( $sSource );
			}
		}
	}

	/**
	 * @return string
	 */
	public function getVisitorAddressSource() {
		return $this->getOpt( 'visitor_address_source' );
	}

	/**
	 * @param string $sSource
	 * @return $this
	 */
	public function setVisitorAddressSource( $sSource ) {
		return $this->setOpt( 'visitor_address_source', $sSource );
	}

	/**
	 * @return string
	 */
	public function isVisitorAddressSourceAutoDetect() {
		return $this->getVisitorAddressSource() == 'AUTO_DETECT_IP';
	}

	protected function frontEndAjaxHandlers() {
		add_action( $this->prefixWpAjax( 'PluginBadgeClose' ), array( $this, 'ajaxPluginBadgeClose' ) );
	}

	/**
	 * @return string
	 */
	public function getCookieIdBadgeState() {
		return $this->prefix( 'badgeState' );
	}

	/**
	 */
	public function ajaxPluginBadgeClose() {
		$bSuccess = $this->loadDataProcessor()
						 ->setCookie(
							 $this->getCookieIdBadgeState(),
							 'closed',
							 DAY_IN_SECONDS
						 );
		$sMessage = $bSuccess ? 'Badge Closed' : 'Badge Not Closed';
		$this->sendAjaxResponse( $bSuccess, array( 'message' => $sMessage ) );
	}

	public function ajaxSetPluginTrackingPermission() {
		$bValid = self::getConn()->getIsValidAdminArea() && $this->checkAjaxNonce();
		if ( $bValid ) {
			$this->setPluginTrackingPermission( (bool)$this->loadDP()->query( 'agree', false ) );
		}
		$this->sendAjaxResponse( $bValid );
	}

	/**
	 * @param bool $bOnOrOff
	 * @return $this
	 */
	public function setPluginTrackingPermission( $bOnOrOff = true ) {
		$this->setOpt( 'enable_tracking', $bOnOrOff ? 'Y' : 'N' )
			 ->setOpt( 'tracking_permission_set_at', $this->loadDP()->time() )
			 ->savePluginOptions();
		return $this;
	}

	/**
	 * @return array
	 */
	public function supplyGoogleRecaptchaConfig() {
		return array(
			'key'    => $this->getOpt( 'google_recaptcha_site_key' ),
			'secret' => $this->getOpt( 'google_recaptcha_secret_key' ),
			'style'  => $this->getOpt( 'google_recaptcha_style' ),
		);
	}

	/**
	 * @param boolean $bGloballyDisabled
	 * @return boolean
	 */
	public function filter_IsPluginGloballyDisabled( $bGloballyDisabled ) {
		return $bGloballyDisabled || !$this->getOptIs( 'global_enable_plugin_features', 'Y' );
	}

	/**
	 * @return array
	 */
	public function getActivePluginFeatures() {
		$aActiveFeatures = $this->getDefinition( 'active_plugin_features' );

		$aPluginFeatures = array();
		if ( !empty( $aActiveFeatures ) && is_array( $aActiveFeatures ) ) {

			foreach ( $aActiveFeatures as $nPosition => $aFeature ) {
				if ( isset( $aFeature[ 'hidden' ] ) && $aFeature[ 'hidden' ] ) {
					continue;
				}
				$aPluginFeatures[ $aFeature[ 'slug' ] ] = $aFeature;
			}
		}
		return $aPluginFeatures;
	}

	/**
	 * @return mixed
	 */
	public function getIsMainFeatureEnabled() {
		return true;
	}

	/**
	 * Hooked to 'deactivate_plugin' and can be used to interrupt the deactivation of this plugin.
	 * @param string $sPlugin
	 */
	public function onWpHookDeactivatePlugin( $sPlugin ) {
		$oCon = self::getConn();
		if ( strpos( $oCon->getRootFile(), $sPlugin ) !== false ) {
			if ( !$oCon->getHasPermissionToManage() ) {
				$this->loadWp()->wpDie(
					_wpsf__( 'Sorry, you do not have permission to disable this plugin.' )
					._wpsf__( 'You need to authenticate first.' )
				);
			}
		}
	}

	/**
	 * @return int
	 */
	public function getTrackingLastSentAt() {
		$nTime = (int)$this->getOpt( 'tracking_last_sent_at', 0 );
		return ( $nTime < 0 ) ? 0 : $nTime;
	}

	/**
	 * @return string
	 */
	public function getLinkToTrackingDataDump() {
		return add_query_arg(
			array( 'shield_action' => 'dump_tracking_data' ),
			$this->loadWp()->getUrl_WpAdmin()
		);
	}

	/**
	 * @return bool
	 */
	public function isTrackingEnabled() {
		return $this->getOptIs( 'enable_tracking', 'Y' );
	}

	/**
	 * @return bool
	 */
	public function isTrackingPermissionSet() {
		return !$this->getOptIs( 'tracking_permission_set_at', 0 );
	}

	/**
	 * @return $this
	 */
	public function setTrackingLastSentAt() {
		return $this->setOpt( 'tracking_last_sent_at', $this->loadDataProcessor()->time() );
	}

	/**
	 * @return bool
	 */
	public function readyToSendTrackingData() {
		return ( ( $this->loadDataProcessor()->time() - $this->getTrackingLastSentAt() ) > WEEK_IN_SECONDS );
	}

	/**
	 * @param $sEmail
	 * @return string
	 */
	public function getPluginReportEmail( $sEmail ) {
		$sReportEmail = $this->getOpt( 'block_send_email_address' );
		if ( $this->loadDataProcessor()->validEmail( $sReportEmail ) ) {
			$sEmail = $sReportEmail;
		}
		return $sEmail;
	}

	/**
	 * This is the point where you would want to do any options verification
	 */
	protected function doPrePluginOptionsSave() {

		$nInstalledAt = $this->getPluginInstallationTime();
		if ( empty( $nInstalledAt ) || $nInstalledAt <= 0 ) {
			$this->setOpt( 'installation_time', $this->loadDataProcessor()->time() );
		}

		if ( $this->isTrackingEnabled() && !$this->isTrackingPermissionSet() ) {
			$this->setOpt( 'tracking_permission_set_at', $this->loadDataProcessor()->time() );
		}

		$this->cleanRecaptchaKey( 'google_recaptcha_site_key' );
		$this->cleanRecaptchaKey( 'google_recaptcha_secret_key' );

		$this->cleanImportExportWhitelistUrls();
		$this->cleanImportExportMasterImportUrl();

		$this->setPluginInstallationId();
	}

	/**
	 * @param string $sOptionKey
	 */
	protected function cleanRecaptchaKey( $sOptionKey ) {
		$sCaptchaKey = trim( (string)$this->getOpt( $sOptionKey, '' ) );
		$nSpacePos = strpos( $sCaptchaKey, ' ' );
		if ( $nSpacePos !== false ) {
			$sCaptchaKey = substr( $sCaptchaKey, 0, $nSpacePos + 1 ); // cut off the string if there's spaces
		}
		$sCaptchaKey = preg_replace( '#[^0-9a-zA-Z_-]#', '', $sCaptchaKey ); // restrict character set
//			if ( strlen( $sCaptchaKey ) != 40 ) {
//				$sCaptchaKey = ''; // need to verify length is 40.
//			}
		$this->setOpt( $sOptionKey, $sCaptchaKey );
	}

	/**
	 * Ensure we always a valid installation ID.
	 * @deprecated but still used because it aligns with stats collection
	 * @return string
	 */
	public function getPluginInstallationId() {
		$sId = $this->getOpt( 'unique_installation_id', '' );

		if ( !$this->isValidInstallId( $sId ) ) {
			$sId = $this->setPluginInstallationId();
		}
		return $sId;
	}

	/**
	 * @param string $sNewId - leave empty to reset if the current isn't valid
	 * @return string
	 */
	protected function setPluginInstallationId( $sNewId = null ) {
		// only reset if it's not of the correct type
		if ( !$this->isValidInstallId( $sNewId ) ) {
			$sNewId = $this->genInstallId();
		}
		$this->setOpt( 'unique_installation_id', $sNewId );
		return $sNewId;
	}

	/**
	 * @return string
	 */
	protected function genInstallId() {
		return sha1(
			$this->getPluginInstallationTime()
			.$this->loadWp()->getWpUrl()
			.$this->loadDbProcessor()->getPrefix()
		);
	}

	/**
	 * @return string
	 */
	public function getImportExportMasterImportUrl() {
		return $this->getOpt( 'importexport_masterurl', '' );
	}

	/**
	 * @return bool
	 */
	public function hasImportExportMasterImportUrl() {
		$sMaster = $this->getImportExportMasterImportUrl();
		return !empty( $sMaster ) && ( rtrim( $this->loadWp()->getHomeUrl(), '/' ) != $sMaster );
	}

	/**
	 * @return int
	 */
	public function getImportExportHandshakeExpiresAt() {
		return $this->getOpt( 'importexport_handshake_expires_at', $this->loadDP()->time() );
	}

	/**
	 * @return string[]
	 */
	public function getImportExportWhitelist() {
		$aWhitelist = $this->getOpt( 'importexport_whitelist', array() );
		return is_array( $aWhitelist ) ? $aWhitelist : array();
	}

	/**
	 * @return string
	 */
	public function getImportExportLastImportHash() {
		return $this->getOpt( 'importexport_last_import_hash', '' );
	}

	/**
	 * @return string
	 */
	protected function getImportExportSecretKey() {
		$sId = $this->getOpt( 'importexport_secretkey', '' );
		if ( empty( $sId ) || $this->isImportExportSecretKeyExpired() ) {
			$sId = sha1( $this->getPluginInstallationId().wp_rand( 0, PHP_INT_MAX ) );
			$this->setOpt( 'importexport_secretkey', $sId )
				 ->setOpt( 'importexport_secretkey_expires_at', $this->loadDP()->time() + HOUR_IN_SECONDS );
		}
		return $sId;
	}

	/**
	 * @return bool
	 */
	public function isImportExportPermitted() {
		return $this->getOptIs( 'importexport_enable', 'Y' );
	}

	/**
	 * @return bool
	 */
	protected function isImportExportSecretKeyExpired() {
		return ( $this->loadDP()->time() > $this->getOpt( 'importexport_secretkey_expires_at' ) );
	}

	/**
	 * @return bool
	 */
	public function isImportExportWhitelistNotify() {
		return $this->getOptIs( 'importexport_whitelist_notify', 'Y' );
	}

	/**
	 * @param string $sUrl
	 * @return $this
	 */
	public function addUrlToImportExportWhitelistUrls( $sUrl ) {
		$sUrl = $this->loadDP()->validateSimpleHttpUrl( $sUrl );
		if ( $sUrl !== false ) {
			$aWhitelistUrls = $this->getImportExportWhitelist();
			$aWhitelistUrls[] = $sUrl;
			$this->setOpt( 'importexport_whitelist', $aWhitelistUrls )
				 ->savePluginOptions();
		}
		return $this;
	}

	/**
	 * @param string $sKey
	 * @return bool
	 */
	public function isImportExportSecretKey( $sKey ) {
		return ( !empty( $sKey ) && $this->getImportExportSecretKey() == $sKey );
	}

	/**
	 * @return $this
	 */
	protected function cleanImportExportWhitelistUrls() {
		$oDP = $this->loadDP();

		$aCleaned = array();
		$aWhitelistUrls = $this->getImportExportWhitelist();
		foreach ( $aWhitelistUrls as $nKey => $sUrl ) {

			$sUrl = $oDP->validateSimpleHttpUrl( $sUrl );
			if ( $sUrl !== false ) {
				$aCleaned[] = $sUrl;
			}
		}
		return $this->setOpt( 'importexport_whitelist', array_unique( $aCleaned ) );
	}

	/**
	 * @return $this
	 */
	protected function cleanImportExportMasterImportUrl() {
		$sUrl = $this->loadDP()->validateSimpleHttpUrl( $this->getImportExportMasterImportUrl() );
		if ( $sUrl === false ) {
			$sUrl = '';
		}
		return $this->setOpt( 'importexport_masterurl', $sUrl );
	}

	/**
	 * @return $this
	 */
	public function startImportExportHandshake() {
		$this->setOpt( 'importexport_handshake_expires_at', $this->loadDP()->time() + 30 )
			 ->savePluginOptions();
		return $this;
	}

	/**
	 * @param string $sHash
	 * @return $this
	 */
	public function setImportExportLastImportHash( $sHash ) {
		return $this->setOpt( 'importexport_last_import_hash', $sHash );
	}

	/**
	 * @param string $sUrl
	 * @return $this
	 */
	public function setImportExportMasterImportUrl( $sUrl ) {
		$this->setOpt( 'importexport_masterurl', $sUrl )
			 ->savePluginOptions(); //saving will clean the URL
		return $this;
	}

	/**
	 * @param string $sId
	 * @return bool
	 */
	protected function isValidInstallId( $sId ) {
		return ( !empty( $sId ) && is_string( $sId ) && strlen( $sId ) == 40 );
	}

	/**
	 * @return array
	 */
	protected function buildIpAddressMap() {
		$aOptionData = $this->getOptionsVo()->getRawData_SingleOption( 'visitor_address_source' );
		$aValueOptions = $aOptionData[ 'value_options' ];

		$oDp = $this->loadDataProcessor();
		$aMap = array();
		$aEmpties = array();
		foreach ( $aValueOptions as $aOptionValue ) {
			$sKey = $aOptionValue[ 'value_key' ];
			if ( $sKey == 'AUTO_DETECT_IP' ) {
				$sKey = 'Auto Detect';
				$sIp = $oDp->loadIpService()->getRequestIp();
			}
			else {
				$sIp = $oDp->FetchServer( $sKey );
			}
			if ( empty( $sIp ) ) {
				$aEmpties[] = sprintf( '%s- %s', $sKey, 'ip not available' );
			}
			else {
				$aMap[] = sprintf( '%s- %s', $sKey, empty( $sIp ) ? 'ip not available' : '<strong>'.$sIp.'</strong>' );
			}
		}
		return array_merge( $aMap, $aEmpties );
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function renderPluginBadge() {
		$oCon = $this->getConn();
		$sContents = $this->loadRenderer( $oCon->getPath_Templates() )
						  ->setTemplateEnginePhp()
						  ->clearRenderVars()
						  ->setRenderVars( $this->getBaseAjaxActionRenderData( 'PluginBadgeClose' ) )
						  ->setTemplate( 'snippets/plugin_badge' )
						  ->render();

		$sBadgeText = sprintf(
			_wpsf__( 'This Site Is Protected By %s' ),
			sprintf(
				'<br /><span style="font-weight: bold;">The %s &rarr;</span>',
				$oCon->getHumanName()
			)
		);
		$sBadgeText = apply_filters( 'icwp_shield_plugin_badge_text', $sBadgeText );
		$bNoFollow = apply_filters( 'icwp_shield_badge_relnofollow', false );
		return sprintf( $sContents,
			$bNoFollow ? 'rel="nofollow"' : '',
			$oCon->getPluginUrl_Image( 'pluginlogo_32x32.png' ),
			$oCon->getHumanName(),
			$sBadgeText
		);
	}

	/**
	 * @return array
	 */
	protected function getDisplayStrings() {
		return array(
			'actions_title'   => _wpsf__( 'Plugin Actions' ),
			'actions_summary' => _wpsf__( 'E.g. Import/Export' ),
		);
	}

	/**
	 * @param array $aOptionsParams
	 * @return array
	 * @throws Exception
	 */
	protected function loadStrings_SectionTitles( $aOptionsParams ) {

		$sSectionSlug = $aOptionsParams[ 'slug' ];
		switch ( $sSectionSlug ) {

			case 'section_global_security_options' :
				$sTitle = _wpsf__( 'Global Plugin Security Options' );
				$sTitleShort = _wpsf__( 'Global Options' );
				break;

			case 'section_defaults' :
				$sTitle = _wpsf__( 'Plugin Defaults' );
				$sTitleShort = _wpsf__( 'Plugin Defaults' );
				break;

			case 'section_importexport' :
				$sTitle = sprintf( '%s / %s', _wpsf__( 'Import' ), _wpsf__( 'Export' ) );
				$aSummary = array(
					sprintf( _wpsf__( 'Purpose - %s' ), _wpsf__( 'Automatically import options, and deploy configurations across your entire network.' ) ),
					sprintf( _wpsf__( 'This is a Pro-only feature.' ) ),
				);
				$sTitleShort = sprintf( '%s / %s', _wpsf__( 'Import' ), _wpsf__( 'Export' ) );
				break;

			case 'section_general_plugin_options' :
				$sTitle = _wpsf__( 'General Plugin Options' );
				$sTitleShort = _wpsf__( 'General Options' );
				break;

			case 'section_third_party_google' :
				$sTitle = _wpsf__( 'Google' );
				$sTitleShort = _wpsf__( 'Google' );
				$aSummary = array(
					sprintf( _wpsf__( 'Purpose - %s' ), _wpsf__( 'Setup Google reCAPTCHA for use across Shield.' ) ),
					sprintf( _wpsf__( 'Recommendation - %s' ), _wpsf__( 'Use of this feature is highly recommend.' ).' '._wpsf__( 'Note: you must create your own Google reCAPTCHA API Keys.' ) ),
					sprintf( _wpsf__( 'Note - %s' ), _wpsf__( 'Invisible Google reCAPTCHA is available with Shield Pro.' ) )
				);
				break;

			case 'section_third_party_duo' :
				$sTitle = _wpsf__( 'Duo Security' );
				$sTitleShort = _wpsf__( 'Duo Security' );
				break;

			default:
				throw new Exception( sprintf( 'A section slug was defined but with no associated strings. Slug: "%s".', $sSectionSlug ) );
		}
		$aOptionsParams[ 'title' ] = $sTitle;
		$aOptionsParams[ 'summary' ] = ( isset( $aSummary ) && is_array( $aSummary ) ) ? $aSummary : array();
		$aOptionsParams[ 'title_short' ] = $sTitleShort;
		return $aOptionsParams;
	}

	/**
	 * @param array $aOptionsParams
	 * @return array
	 * @throws Exception
	 */
	protected function loadStrings_Options( $aOptionsParams ) {

		$sKey = $aOptionsParams[ 'key' ];
		switch ( $sKey ) {

			case 'global_enable_plugin_features' :
				$sName = _wpsf__( 'Enable Plugin Features' );
				$sSummary = _wpsf__( 'Global Plugin On/Off Switch' );
				$sDescription = sprintf( _wpsf__( 'Uncheck this option to disable all %s features.' ), self::getConn()
																										   ->getHumanName() );
				break;

			case 'enable_tracking' :
				$sName = sprintf( _wpsf__( 'Enable %s' ), _wpsf__( 'Information Gathering' ) );
				$sSummary = _wpsf__( 'Permit Anonymous Usage Information Gathering' );
				$sDescription = _wpsf__( 'Allows us to gather information on statistics and features in-use across our client installations.' )
								.' '._wpsf__( 'This information is strictly anonymous and contains no personally, or otherwise, identifiable data.' )
								.'<br />'.sprintf( '<a href="%s" target="_blank">%s</a>', $this->getLinkToTrackingDataDump(), _wpsf__( 'Click to see the exact data that would be sent.' ) );
				break;

			case 'visitor_address_source' :
				$sName = _wpsf__( 'IP Source' );
				$sSummary = _wpsf__( 'Which IP Address Is Yours' );
				$sDescription = _wpsf__( 'There are many possible ways to detect visitor IP addresses. If Auto-Detect is not working, please select yours from the list.' )
								.'<br />'._wpsf__( 'If the option you select becomes unavailable, we will revert to auto detection.' )
								.'<br />'.sprintf( _wpsf__( 'Current source is: %s' ), '<strong>'.$this->getVisitorAddressSource().'</strong>' )
								.'<br />'.implode( '<br />', $this->buildIpAddressMap() );
				break;

			case 'block_send_email_address' :
				$sName = _wpsf__( 'Report Email' );
				$sSummary = _wpsf__( 'Where to send email reports' );
				$sDescription = sprintf( _wpsf__( 'If this is empty, it will default to the blog admin email address: %s' ), '<br /><strong>'.get_bloginfo( 'admin_email' ).'</strong>' );
				break;

			case 'enable_upgrade_admin_notice' :
				$sName = _wpsf__( 'In-Plugin Notices' );
				$sSummary = _wpsf__( 'Display Plugin Specific Notices' );
				$sDescription = _wpsf__( 'Disable this option to hide certain plugin admin notices about available updates and post-update notices.' );
				break;

			case 'display_plugin_badge' :
				$sName = _wpsf__( 'Show Plugin Badge' );
				$sSummary = _wpsf__( 'Display Plugin Badge On Your Site' );
				$sDescription = _wpsf__( 'Enabling this option helps support the plugin by spreading the word about it on your website.' )
								.' '._wpsf__( 'The plugin badge also lets visitors know your are taking your website security seriously.' )
								.sprintf( '<br /><strong><a href="%s" target="_blank">%s</a></strong>', 'http://icwp.io/wpsf20', _wpsf__( 'Read this carefully before enabling this option.' ) );
				break;

			case 'delete_on_deactivate' :
				$sName = _wpsf__( 'Delete Plugin Settings' );
				$sSummary = _wpsf__( 'Delete All Plugin Settings Upon Plugin Deactivation' );
				$sDescription = _wpsf__( 'Careful: Removes all plugin options when you deactivate the plugin' );
				break;

			case 'importexport_enable' :
				$sName = _wpsf__( 'Allow Import/Export' );
				$sSummary = _wpsf__( 'Allow Import And Export Of Options On This Site' );
				$sDescription = _wpsf__( 'Uncheck this box to completely disable import and export of options.' )
								.'<br />'.sprintf( '%s: %s', _wpsf__( 'Note' ), _wpsf__( 'Import/Export is a premium-only feature.' ) );
				break;

			case 'importexport_whitelist' :
				$sName = _wpsf__( 'Export Whitelist' );
				$sSummary = _wpsf__( 'Whitelisted Sites To Export Options From This Site' );
				$sDescription = _wpsf__( 'Whitelisted sites may export options from this site without the key.' )
								.'<br />'._wpsf__( 'List each site URL on a new line.' )
								.'<br />'._wpsf__( 'This is to be used in conjunction with the Master Import Site feature.' );
				break;

			case 'importexport_masterurl' :
				$sName = _wpsf__( 'Master Import Site' );
				$sSummary = _wpsf__( 'Automatically Import Options From This Site URL' );
				$sDescription = _wpsf__( "Supplying a site URL here will make this site an 'Options Slave'." )
								.'<br />'._wpsf__( 'Options will be automatically imported from the Master Import site each day.' )
								.'<br />'.sprintf( '%s: %s', _wpsf__( 'Warning' ), _wpsf__( 'Use of this feature will overwrite existing options and replace them with those from the Master Import Site.' ) );
				break;

			case 'importexport_whitelist_notify' :
				$sName = _wpsf__( 'Notify Whitelist' );
				$sSummary = _wpsf__( 'Notify Sites On The Whitelist To Update Options From Master' );
				$sDescription = _wpsf__( "When enabled, manual options saving will notify sites on the whitelist to export options from the Master site." );
				break;

			case 'importexport_secretkey' :
				$sName = _wpsf__( 'Secret Key' );
				$sSummary = _wpsf__( 'Import/Export Secret Key' );
				$sDescription = _wpsf__( 'Keep this Secret Key private as it will allow the import and export of options.' );
				break;

			case 'unique_installation_id' :
				$sName = _wpsf__( 'Installation ID' );
				$sSummary = _wpsf__( 'Unique Plugin Installation ID' );
				$sDescription = _wpsf__( 'Keep this ID private.' );
				break;

			case 'google_recaptcha_secret_key' :
				$sName = _wpsf__( 'reCAPTCHA Secret' );
				$sSummary = _wpsf__( 'Google reCAPTCHA Secret Key' );
				$sDescription = _wpsf__( 'Enter your Google reCAPTCHA secret key for use throughout the plugin.' );
				break;

			case 'google_recaptcha_site_key' :
				$sName = _wpsf__( 'reCAPTCHA Site Key' );
				$sSummary = _wpsf__( 'Google reCAPTCHA Site Key' );
				$sDescription = _wpsf__( 'Enter your Google reCAPTCHA site key for use throughout the plugin' );
				break;

			case 'google_recaptcha_style' :
				$sName = _wpsf__( 'reCAPTCHA Style' );
				$sSummary = _wpsf__( 'How Google reCAPTCHA Will Be Displayed By Default' );
				$sDescription = _wpsf__( 'You can choose the reCAPTCHA display format that best suits your site, including the new Invisible Recaptcha' );
				break;

			default:
				throw new Exception( sprintf( 'An option has been defined but without strings assigned to it. Option key: "%s".', $sKey ) );
		}

		$aOptionsParams[ 'name' ] = $sName;
		$aOptionsParams[ 'summary' ] = $sSummary;
		$aOptionsParams[ 'description' ] = $sDescription;
		return $aOptionsParams;
	}

	/**
	 * Kept just in-case.
	 */
	protected function old_translations() {
		_wpsf__( 'IP Whitelist' );
		_wpsf__( 'IP Address White List' );
		_wpsf__( 'Any IP addresses on this list will by-pass all Plugin Security Checking.' );
		_wpsf__( 'Your IP address is: %s' );
		_wpsf__( 'Choose IP Addresses To Blacklist' );
		_wpsf__( 'Recommendation - %s' );
		_wpsf__( 'Blacklist' );
		_wpsf__( 'Logging' );
		_wpsf__( 'User "%s" was forcefully logged out as they were not verified by either cookie or IP address (or both).' );
		_wpsf__( 'User "%s" was found to be un-verified at the given IP Address: "%s".' );
		_wpsf__( 'Cookie' );
		_wpsf__( 'IP Address' );
		_wpsf__( 'IP' );
		_wpsf__( 'This will restrict all user login sessions to a single browser. Use this if your users have dynamic IP addresses.' );
		_wpsf__( 'All users will be required to authenticate their login by email-based two-factor authentication, when logging in from a new IP address' );
		_wpsf__( '2-Factor Auth' );
		_wpsf__( 'Include Logged-In Users' );
		_wpsf__( 'You may also enable GASP for logged in users' );
		_wpsf__( 'Since logged-in users would be expected to be vetted already, this is off by default.' );
		_wpsf__( 'Security Admin' );
		_wpsf__( 'Protect your security plugin not just your WordPress site' );
		_wpsf__( 'Security Admin' );
		_wpsf__( 'Audit Trail' );
		_wpsf__( 'Get a view on what happens on your site, when it happens' );
		_wpsf__( 'Audit Trail Viewer' );
		_wpsf__( 'Automatic Updates' );
		_wpsf__( 'Take back full control of WordPress automatic updates' );
		_wpsf__( 'Comments SPAM' );
		_wpsf__( 'Block comment SPAM and retain your privacy' );
		_wpsf__( 'Email' );
		_wpsf__( 'Firewall' );
		_wpsf__( 'Automatically block malicious URLs and data sent to your site' );
		_wpsf__( 'Hack Protection' );
		_wpsf__( 'HTTP Headers' );
		_wpsf__( 'Control HTTP Security Headers' );
		_wpsf__( 'IP Manager' );
		_wpsf__( 'Manage Visitor IP Address' );
		_wpsf__( 'Lockdown' );
		_wpsf__( 'Harden the more loosely controlled settings of your site' );
		_wpsf__( 'Login Protection' );
		_wpsf__( 'Block brute force attacks and secure user identities with Two-Factor Authentication' );
		_wpsf__( 'Dashboard' );
		_wpsf__( 'Overview of the plugin settings' );
		_wpsf__( 'Statistics' );
		_wpsf__( 'Summary of the main security actions taken by this plugin' );
		_wpsf__( 'Stats Viewer' );
		_wpsf__( 'Premium Support' );
		_wpsf__( 'Premium Plugin Support Centre' );
		_wpsf__( 'User Management' );
		_wpsf__( 'Get true user sessions and control account sharing, session duration and timeouts' );
		_wpsf__( 'Two-Factor Authentication' );
	}
}