<?php
if ( !class_exists( 'ICWP_WPSF_WpFunctions', false ) ):

	class ICWP_WPSF_WpFunctions extends ICWP_WPSF_Foundation {

		/**
		 * @var WP_Automatic_Updater
		 */
		protected $oWpAutomaticUpdater;

		/**
		 * @var ICWP_WPSF_WpFunctions
		 */
		protected static $oInstance = NULL;

		/**
		 * @return ICWP_WPSF_WpFunctions
		 */
		public static function GetInstance() {
			if ( is_null( self::$oInstance ) ) {
				self::$oInstance = new self();
			}
			return self::$oInstance;
		}

		/**
		 * @var string
		 */
		protected $sWpVersion;

		/**
		 * @var boolean
		 */
		protected $bIsMultisite;

		public function __construct() {}

		/**
		 * @return null|string
		 */
		public function findWpLoad() {
			return $this->findWpCoreFile( 'wp-load.php' );
		}

		/**
		 * @param $sFilename
		 * @return null|string
		 */
		public function findWpCoreFile( $sFilename ) {
			$sLoaderPath	= dirname( __FILE__ );
			$nLimiter		= 0;
			$nMaxLimit		= count( explode( DIRECTORY_SEPARATOR, trim( $sLoaderPath, DIRECTORY_SEPARATOR ) ) );
			$bFound			= false;

			do {
				if ( @is_file( $sLoaderPath.DIRECTORY_SEPARATOR.$sFilename ) ) {
					$bFound = true;
					break;
				}
				$sLoaderPath = realpath( $sLoaderPath.DIRECTORY_SEPARATOR.'..' );
				$nLimiter++;
			}
			while ( $nLimiter < $nMaxLimit );

			return $bFound ? $sLoaderPath.DIRECTORY_SEPARATOR.$sFilename : null;
		}

		/**
		 * @param string $sRedirect
		 *
		 * @return bool
		 */
		public function doForceRunAutomaticUpdates( $sRedirect = '' ) {

			$lock_name = 'auto_updater.lock'; //ref: /wp-admin/includes/class-wp-upgrader.php
			delete_option( $lock_name );
			if ( !defined('DOING_CRON') ) {
				define( 'DOING_CRON', true ); // this prevents WP from disabling plugins pre-upgrade
			}

			// does the actual updating
			wp_maybe_auto_update();

			if ( !empty( $sRedirect ) ) {
				$this->doRedirect( network_admin_url( $sRedirect ) );
			}
			return true;
		}

		/**
		 * @return bool
		 */
		public function getIsRunningAutomaticUpdates() {
			return ( get_option( 'auto_updater.lock' ) ? true : false );
		}

		/**
		 * The full plugin file to be upgraded.
		 *
		 * @param string $sPluginFile
		 * @return boolean
		 */
		public function doPluginUpgrade( $sPluginFile ) {

			if ( !$this->getIsPluginUpdateAvailable( $sPluginFile )
				|| ( isset( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] == 'update.php' ) ) {
				return true;
			}
			$sUrl = $this->getPluginUpgradeLink( $sPluginFile );
			wp_redirect( $sUrl );
			exit();
		}

		/**
		 * Clears any WordPress caches
		 */
		public function doBustCache() {
			global $_wp_using_ext_object_cache, $wp_object_cache;
			$_wp_using_ext_object_cache = false;
			if( !empty( $wp_object_cache ) ) {
				@$wp_object_cache->flush();
			}
		}

		/**
		 * @return bool
		 */
		public function getIsPermalinksEnabled() {
			return ( $this->getOption( 'permalink_structure' ) ? true : false );
		}

		/**
		 * @see wp_redirect_admin_locations()
		 * @return array
		 */
		public function getAutoRedirectLocations() {
			return array( 'wp-admin', 'dashboard', 'admin', 'login', 'wp-login.php' );
		}

		/**
		 * @return array|false
		 */
		public function getCoreChecksums() {
			$aChecksumData = false;
			$sCurrentVersion = $this->getWordpressVersion();

			if ( function_exists( 'get_core_checksums' ) ) { // if it's loaded, we use it.
				$aChecksumData = get_core_checksums( $sCurrentVersion, $this->getLocale( true ) );
			}
			else {
				$aQueryArgs = array(
					'version' 	=> $sCurrentVersion,
					'locale'	=> $this->getLocale( true )
				);
				$sQueryUrl = add_query_arg( $aQueryArgs, 'https://api.wordpress.org/core/checksums/1.0/' );
				$sResponse = $this->loadFS()->getUrlContent( $sQueryUrl );
				if ( !empty( $sResponse ) ) {
					$aDecodedResponse = json_decode( trim( $sResponse ), true );
					if ( is_array( $aDecodedResponse ) && isset( $aDecodedResponse['checksums'] ) && is_array( $aDecodedResponse['checksums'] ) ) {
						$aChecksumData = $aDecodedResponse[ 'checksums' ];
					}
				}
			}
			return $aChecksumData;
		}

		/**
		 * @param string $sPath
		 * @return string
		 */
		public function getUrl_WpAdmin( $sPath = '' ) {
			return get_admin_url( null, $sPath );
		}

		/**
		 * @param bool $bRemoveSchema
		 * @return string
		 */
		public function getHomeUrl( $bRemoveSchema = false ) {
			$sUrl = home_url();
			if ( empty( $sUrl ) ) {
				remove_all_filters( 'home_url' );
				$sUrl = home_url();
			}
			if ( $bRemoveSchema ) {
				$sUrl = preg_replace( '#^((http|https):)?\/\/#i', '', $sUrl );
			}
			return $sUrl;
		}

		/**
		 * @param bool $bRemoveSchema
		 * @return string
		 */
		public function getWpUrl( $bRemoveSchema = false ) {
			$sUrl = network_site_url();
			if ( empty( $sUrl ) ) {
				remove_all_filters( 'site_url' );
				remove_all_filters( 'network_site_url' );
				$sUrl = network_site_url();
			}
			if ( $bRemoveSchema ) {
				$sUrl = preg_replace( '#^((http|https):)?\/\/#i', '', $sUrl );
			}
			return $sUrl;
		}

		/**
		 * @param bool $bForChecksums
		 * @return string
		 */
		public function getLocale( $bForChecksums = false ) {
			$sLocale = get_locale();
			if ( $bForChecksums ) {
				global $wp_local_package;
				$sLocale = empty( $wp_local_package ) ? 'en_US' : $wp_local_package;
			}
			return $sLocale;
		}

		/**
		 * @return array
		 */
		public function getPlugins() {
			if ( !function_exists( 'get_plugins' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			return function_exists( 'get_plugins' ) ? get_plugins() : array();
		}

		/**
		 * @param string $sRootPluginFile - the full path to the root plugin file
		 * @return array|null
		 */
		public function getPluginData( $sRootPluginFile ) {
			if ( !function_exists( 'get_plugin_data' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			return function_exists( 'get_plugin_data' ) ? get_plugin_data( $sRootPluginFile, false, false ) : array();
		}

		/**
		 * @param string $sPluginFile
		 * @return stdClass|null
		 */
		public function getPluginUpdateInfo( $sPluginFile ) {
			$aUpdates = $this->getWordpressUpdates_Plugins();
			return ( !empty( $aUpdates ) && isset( $aUpdates[ $sPluginFile ] ) ) ? $aUpdates[ $sPluginFile ] : null;
		}

		/**
		 * @param string $sPluginFile
		 * @return string
		 */
		public function getPluginUpdateNewVersion( $sPluginFile ) {
			$oInfo = $this->getPluginUpdateInfo( $sPluginFile );
			return ( !is_null( $oInfo ) && isset( $oInfo->new_version ) ) ? $oInfo->new_version : '';
		}

		/**
		 * @param string $sPluginFile
		 * @return boolean|stdClass
		 */
		public function getIsPluginUpdateAvailable( $sPluginFile ) {
			$oInfo = $this->getPluginUpdateInfo( $sPluginFile );
			return !is_null( $oInfo );
		}

		/**
		 * @param string $sCompareString
		 * @param string $sKey
		 * @return bool
		 */
		public function getIsPluginActive( $sCompareString, $sKey = 'Name' ) {
			$sPluginFile = $this->getIsPluginInstalled( $sCompareString, $sKey );
			if ( !$sPluginFile ) {
				return false;
			}
			return is_plugin_active( $sPluginFile ) ? $sPluginFile : false;
		}

		/**
		 * @param string $sCompareString
		 * @param string $sKey
		 * @return bool|string
		 */
		public function getIsPluginInstalled( $sCompareString, $sKey = 'Name' ) {
			$aPlugins = $this->getPlugins();
			if ( empty( $aPlugins ) || !is_array( $aPlugins ) ) {
				return false;
			}

			foreach( $aPlugins as $sBaseFileName => $aPluginData ) {
				if ( isset( $aPluginData[$sKey] ) && $sCompareString == $aPluginData[$sKey] ) {
					return $sBaseFileName;
				}
			}
			return false;
		}

		/**
		 * @param string $sPluginBaseFile
		 * @return bool
		 */
		public function getIsPluginInstalledByFile( $sPluginBaseFile ) {
			$aPlugins = $this->getPlugins();
			return isset( $aPlugins[$sPluginBaseFile] );
		}

		/**
		 * @param string $sPluginFile
		 * @return string
		 */
		public function getPluginActivateLink( $sPluginFile ) {
			$sUrl = self_admin_url( 'plugins.php' ) ;
			$aQueryArgs = array(
				'action' 	=> 'activate',
				'plugin'	=> urlencode( $sPluginFile ),
				'_wpnonce'	=> wp_create_nonce( 'activate-plugin_' . $sPluginFile )
			);
			return add_query_arg( $aQueryArgs, $sUrl );
		}

		/**
		 * @param string $sPluginFile
		 * @return string
		 */
		public function getPluginDeactivateLink( $sPluginFile ) {
			$sUrl = self_admin_url( 'plugins.php' ) ;
			$aQueryArgs = array(
				'action' 	=> 'deactivate',
				'plugin'	=> urlencode( $sPluginFile ),
				'_wpnonce'	=> wp_create_nonce( 'deactivate-plugin_' . $sPluginFile )
			);
			return add_query_arg( $aQueryArgs, $sUrl );
		}

		/**
		 * @param string $sPluginFile
		 * @return string
		 */
		public function getPluginUpgradeLink( $sPluginFile ) {
			$sUrl = self_admin_url( 'update.php' ) ;
			$aQueryArgs = array(
				'action' 	=> 'upgrade-plugin',
				'plugin'	=> urlencode( $sPluginFile ),
				'_wpnonce'	=> wp_create_nonce( 'upgrade-plugin_' . $sPluginFile )
			);
			return add_query_arg( $aQueryArgs, $sUrl );
		}

		/**
		 * @param stdClass|string $mItem
		 * @param string          $sContext from plugin|theme
		 * @return string
		 */
		public function getFileFromAutomaticUpdateItem( $mItem, $sContext = 'plugin' ) {
			if ( is_object( $mItem ) && isset( $mItem->{$sContext} ) )  { // WP 3.8.2+
				$mItem = $mItem->{$sContext};
			}
			else if ( !is_string( $mItem ) ) { // WP pre-3.8.2
				$mItem = '';
			}
			return $mItem;
		}

		/**
		 * @return array
		 */
		public function getThemes() {
			if ( !function_exists( 'wp_get_themes' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/theme.php' );
			}
			return function_exists( 'wp_get_themes' ) ? wp_get_themes() : array();
		}

		/**
		 * @param string $sType - plugins, themes
		 * @return array
		 */
		public function getWordpressUpdates( $sType = 'plugins' ) {
			$oCurrent = $this->getTransient( 'update_'.$sType );
			return ( isset( $oCurrent->response ) && is_array( $oCurrent->response ) ) ? $oCurrent->response : array();
		}

		/**
		 * @return array
		 */
		public function getWordpressUpdates_Plugins() {
			return $this->getWordpressUpdates( 'plugins' );
		}

		/**
		 * @return array
		 */
		public function getWordpressUpdates_Themes() {
			return $this->getWordpressUpdates( 'themes' );
		}

		/**
		 * @param string $sKey
		 * @return mixed
		 */
		public function getTransient( $sKey ) {
			// TODO: Handle multisite

			if ( function_exists( 'get_site_transient' ) ) {
				$mResult = get_site_transient( $sKey );
				if ( empty( $mResult ) ) {
					remove_all_filters( 'pre_site_transient_'.$sKey );
					$mResult = get_site_transient( $sKey );
				}
			}
			else if ( version_compare( $this->getWordpressVersion(), '2.7.9', '<=' ) ) {
				$mResult = get_option( $sKey );
			}
			else if ( version_compare( $this->getWordpressVersion(), '2.9.9', '<=' ) ) {
				$mResult = apply_filters( 'transient_'.$sKey, get_option( '_transient_'.$sKey ) );
			}
			else {
				$mResult = apply_filters( 'site_transient_'.$sKey, get_option( '_site_transient_'.$sKey ) );
			}
			return $mResult;
		}

		/**
		 * @param string $sKey
		 * @param mixed $mValue
		 * @param int $nExpire
		 * @return bool
		 */
		public function setTransient( $sKey, $mValue, $nExpire = 0 ) {
			return set_site_transient( $sKey, $mValue, $nExpire );
		}

		/**
		 * @param $sKey
		 * @return bool
		 */
		public function deleteTransient( $sKey ) {

			if ( version_compare( $this->getWordpressVersion(), '2.7.9', '<=' ) ) {
				$bResult = delete_option( $sKey );
			}
			else if ( function_exists( 'delete_site_transient' ) ) {
				$bResult = delete_site_transient( $sKey );
			}
			else if ( version_compare( $this->getWordpressVersion(), '2.9.9', '<=' ) ) {
				$bResult = delete_option( '_transient_'.$sKey );
			}
			else {
				$bResult = delete_option( '_site_transient_'.$sKey );
			}
			return $bResult;
		}

		/**
		 * @param string $sPluginBaseFilename
		 * @return null|stdClass
		 */
		public function getPluginDataAsObject( $sPluginBaseFilename ){
			$aPlugins = get_plugins();
			if ( !isset( $aPlugins[$sPluginBaseFilename] ) || !is_array( $aPlugins[$sPluginBaseFilename] ) ) {
				return null;
			}

			return $this->loadDataProcessor()->convertArrayToStdClass( $aPlugins[ $sPluginBaseFilename ] );
		}

		/**
		 * @return string
		 */
		public function getWordpressVersion() {

			if ( empty( $this->sWpVersion ) ) {
				$sVersionFile = ABSPATH.WPINC.'/version.php';
				$sVersionContents = file_get_contents( $sVersionFile );

				if ( preg_match( '/wp_version\s=\s\'([^(\'|")]+)\'/i', $sVersionContents, $aMatches ) ) {
					$this->sWpVersion = $aMatches[1];
				}
				else {
					global $wp_version;
					$this->sWpVersion = $wp_version;
				}
			}
			return $this->sWpVersion;
		}

		/**
		 * @param string $sVersionToMeet
		 *
		 * @return boolean
		 */
		public function getWordpressIsAtLeastVersion( $sVersionToMeet ) {
			return version_compare( $this->getWordpressVersion(), $sVersionToMeet, '>=' );
		}

		/**
		 * @param string $sPluginBaseFilename
		 * @return boolean
		 */
		public function getIsPluginAutomaticallyUpdated( $sPluginBaseFilename ) {
			$oUpdater = $this->getWpAutomaticUpdater();
			if ( !$oUpdater ) {
				return false;
			}

			// This is due to a change in the filter introduced in version 3.8.2
			if ( $this->getWordpressIsAtLeastVersion( '3.8.2' ) ) {
				$mPluginItem = new stdClass();
				$mPluginItem->plugin = $sPluginBaseFilename;
			}
			else {
				$mPluginItem = $sPluginBaseFilename;
			}

			return $oUpdater->should_update( 'plugin', $mPluginItem, WP_PLUGIN_DIR );
		}

		public function redirectHere() {
			$this->doRedirect( $this->loadDataProcessor()->getRequestUri() );
		}

		/**
		 * @param array $aQueryParams
		 */
		public function redirectToLogin( $aQueryParams = array() ) {
			$this->doRedirect( wp_login_url(), $aQueryParams );
		}
		/**
		 * @param array $aQueryParams
		 */
		public function redirectToAdmin( $aQueryParams = array() ) {
			$this->doRedirect( is_multisite()? get_admin_url() : admin_url(), $aQueryParams );
		}
		/**
		 * @param array $aQueryParams
		 */
		public function redirectToHome( $aQueryParams = array() ) {
			$this->doRedirect( home_url(), $aQueryParams );
		}

		/**
		 * @param string $sUrl
		 * @param array $aQueryParams
		 * @param bool $bSafe
		 * @param bool $bProtectAgainstInfiniteLoops - if false, ignores the redirect loop protection
		 */
		public function doRedirect( $sUrl, $aQueryParams = array(), $bSafe = true, $bProtectAgainstInfiniteLoops = true ) {
			$sUrl = empty( $aQueryParams ) ? $sUrl : add_query_arg( $aQueryParams, $sUrl );

			$oDp = $this->loadDataProcessor();
			// we prevent any repetitive redirect loops
			if ( $bProtectAgainstInfiniteLoops ) {
				if ( $oDp->FetchCookie( 'icwp-isredirect' ) == 'yes' ) {
					return;
				}
				else {
					$oDp->setCookie( 'icwp-isredirect', 'yes', 2 );
				}
			}

			// based on: https://make.wordpress.org/plugins/2015/04/20/fixing-add_query_arg-and-remove_query_arg-usage/
			// we now escape the URL to be absolutely sure since we can't guarantee the URL coming through there
			$sUrl = esc_url_raw( $sUrl );
			$bSafe ? wp_safe_redirect( $sUrl ) : wp_redirect( $sUrl );
			exit();
		}

		/**
		 * @return string
		 */
		public function getCurrentPage() {
			global $pagenow;
			return $pagenow;
		}

		/**
		 * @return WP_Post
		 */
		public function getCurrentPost() {
			global $post;
			return $post;
		}

		/**
		 * @return int
		 */
		public function getCurrentPostId() {
			$oPost = $this->getCurrentPost();
			return empty( $oPost->ID ) ? -1 : $oPost->ID;
		}

		/**
		 * @param $nPostId
		 * @return false|WP_Post
		 */
		public function getPostById( $nPostId ) {
			return WP_Post::get_instance( $nPostId );
		}

		/**
		 * @return string
		 */
		public function getUrl_CurrentAdminPage() {

			$sPage = $this->getCurrentPage();
			$sUrl = self_admin_url( $sPage );

			//special case for plugin admin pages.
			if ( $sPage == 'admin.php' ) {
				$sSubPage = $this->loadDataProcessor()->FetchGet( 'page' );
				if ( !empty( $sSubPage ) ) {
					$aQueryArgs = array(
						'page' 	=> $sSubPage,
					);
					$sUrl = add_query_arg( $aQueryArgs, $sUrl );
				}
			}
			return $sUrl;
		}

		/**
		 * @param string
		 * @return string
		 */
		public function getIsCurrentPage( $sPage ) {
			return $sPage == $this->getCurrentPage();
		}

		/**
		 * @param string
		 * @return string
		 */
		public function getIsPage_Updates() {
			return $this->getIsCurrentPage( 'update.php' );
		}

		/**
		 * @return bool
		 */
		public function getIsLoginRequest() {
			$oDp = $this->loadDataProcessor();
			return
				$oDp->GetIsRequestPost()
				&& !is_null( $oDp->FetchPost( 'log' ) )
				&& !is_null( $oDp->FetchPost( 'pwd' ) )
				&& $this->getIsLoginUrl();
		}

		/**
		 * @return bool
		 */
		public function getIsRegisterRequest() {
			$oDp = $this->loadDataProcessor();
			return
				$oDp->GetIsRequestPost()
				&& !is_null( $oDp->FetchPost( 'user_login' ) )
				&& !is_null( $oDp->FetchPost( 'user_email' ) )
				&& $this->getIsLoginUrl();
		}

		/**
		 * @return bool
		 */
		public function getIsLoginUrl() {
			$aLoginUrlParts = @parse_url( wp_login_url() );
			$aRequestParts = $this->loadDataProcessor()->getRequestUriParts();
			return ( !empty( $aRequestParts['path'] ) && ( rtrim( $aRequestParts['path'], '/' ) == rtrim( $aLoginUrlParts['path'], '/' ) ) );
		}

		/**
		 * @param $sTermSlug
		 * @return bool
		 */
		public function getDoesWpSlugExist( $sTermSlug ) {
			return ( $this->getDoesWpPostSlugExist( $sTermSlug ) || term_exists( $sTermSlug ) );
		}

		/**
		 * @param $sTermSlug
		 * @return bool
		 */
		public function getDoesWpPostSlugExist( $sTermSlug ) {
			$oDb = $this->loadDbProcessor();
			$sQuery = "
				SELECT ID
				FROM %s
				WHERE
					post_name = '%s'
					LIMIT 1
			";
			$sQuery = sprintf(
				$sQuery,
				$oDb->getTable_Posts(),
				$sTermSlug
			);
			$nResult = $oDb->getVar( $sQuery );
			return !is_null( $nResult ) && $nResult > 0;
		}

		/**
		 * @return string
		 */
		public function getSiteName() {
			return function_exists( 'get_bloginfo' )? get_bloginfo('name') : 'WordPress Site';
		}
		/**
		 * @return string
		 */
		public function getSiteAdminEmail() {
			return function_exists( 'get_bloginfo' )? get_bloginfo('admin_email') : '';
		}

		/**
		 * @return string
		 */
		public function getCookieDomain() {
			return defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : false;
		}

		/**
		 * @return string
		 */
		public function getCookiePath() {
			return defined( 'COOKIEPATH' ) ? COOKIEPATH : '/';
		}

		/**
		 * @return boolean
		 */
		public function getIsAjax() {
			return defined( 'DOING_AJAX' ) && DOING_AJAX;
		}

		/**
		 * @return boolean
		 */
		public function getIsCron() {
			return defined( 'DOING_CRON' ) && DOING_CRON;
		}

		/**
		 * @return boolean
		 */
		public function getIsXmlrpc() {
			return defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST;
		}

		/**
		 * @return boolean
		 */
		public function getIsMobile() {
			return function_exists( 'wp_is_mobile' )&& wp_is_mobile();
		}

		/**
		 * @return array
		 */
		public function getAllUserLoginUsernames() {
			$aUsers = get_users( array( 'fields' => array( 'user_login' ) ) );
			$aLogins = array();
			foreach( $aUsers as $oUser ) {
				$aLogins[] = $oUser->user_login;
			}
			return $aLogins;
		}

		/**
		 * @return bool
		 */
		public function isMultisite() {
			if ( !isset( $this->bIsMultisite ) ) {
				$this->bIsMultisite = function_exists( 'is_multisite' ) && is_multisite();
			}
			return $this->bIsMultisite;
		}

		/**
		 * @param string $sKey
		 * @param string $sValue
		 * @return bool
		 */
		public function addOption( $sKey, $sValue ) {
			return $this->isMultisite() ? add_site_option( $sKey, $sValue ) : add_option( $sKey, $sValue );
		}

		/**
		 * @param string $sKey
		 * @param $sValue
		 * @return boolean
		 */
		public function updateOption( $sKey, $sValue ) {
			return $this->isMultisite() ? update_site_option( $sKey, $sValue ) : update_option( $sKey, $sValue );
		}

		/**
		 * @param string $sKey
		 * @param mixed $mDefault
		 * @return mixed
		 */
		public function getOption( $sKey, $mDefault = false ) {
			return $this->isMultisite() ? get_site_option( $sKey, $mDefault ) : get_option( $sKey, $mDefault );
		}

		/**
		 * @param string $sKey
		 * @return mixed
		 */
		public function deleteOption( $sKey ) {
			return $this->isMultisite() ? delete_site_option( $sKey ) : delete_option( $sKey );
		}

		/**
		 * @return string
		 */
		public function getCurrentWpAdminPage() {

			$oDp = $this->loadDataProcessor();
			$sScript = $oDp->FetchServer( 'SCRIPT_NAME' );
			if ( empty( $sScript ) ) {
				$sScript = $oDp->FetchServer( 'PHP_SELF' );
			}
			if ( is_admin() && !empty( $sScript ) && basename( $sScript ) == 'admin.php' ) {
				$sCurrentPage = $oDp->FetchGet( 'page' );
			}
			return empty( $sCurrentPage ) ? '' : $sCurrentPage;
		}

		/**
		 * @param string $sPluginFile
		 * @return int
		 */
		public function getActivePluginLoadPosition( $sPluginFile ) {
			$nPosition = array_search( $sPluginFile, $this->getActivePlugins() );
			return ( $nPosition === false ) ? -1 : $nPosition;
		}

		/**
		 * @return array
		 */
		public function getActivePlugins() {
			$sOptionKey = $this->isMultisite() ? 'active_sitewide_plugins' : 'active_plugins';
			return $this->getOption( $sOptionKey );
		}

		/**
		 * @param string $sPluginFile
		 * @param int $nDesiredPosition
		 */
		public function setActivePluginLoadPosition( $sPluginFile, $nDesiredPosition = 0 ) {

			$aActive = $this->loadDataProcessor()->setArrayValueToPosition( $this->getOption( 'active_plugins' ), $sPluginFile, $nDesiredPosition );
			$this->updateOption( 'active_plugins', $aActive );

			if ( $this->isMultisite() ) {
				$aActive = $this->loadDataProcessor()->setArrayValueToPosition( $this->getOption( 'active_sitewide_plugins' ), $sPluginFile, $nDesiredPosition );
				$this->updateOption( 'active_sitewide_plugins', $aActive );
			}
		}

		/**
		 * @param string $sPluginFile
		 */
		public function setActivePluginLoadFirst( $sPluginFile ) {
			$this->setActivePluginLoadPosition( $sPluginFile, 0 );
		}

		/**
		 * @param string $sPluginFile
		 */
		public function setActivePluginLoadLast( $sPluginFile ) {
			$this->setActivePluginLoadPosition( $sPluginFile, 1000 );
		}

		/**
		 * @param int|null $nTime
		 * @param bool $bShowTime
		 * @param bool $bShowDate
		 * @return string
		 */
		public function getTimeStringForDisplay( $nTime = null, $bShowTime = true, $bShowDate = true ) {
			$nTime = empty( $nTime ) ? $this->loadDataProcessor()->time() : $nTime;

			$sFullTimeString = $bShowTime ? $this->getTimeFormat() : '';
			if ( empty( $sFullTimeString ) ) {
				$sFullTimeString = $bShowDate ? $this->getDateFormat() : '';
			}
			else {
				$sFullTimeString = $bShowDate ? ( $sFullTimeString . ' '. $this->getDateFormat() ) : $sFullTimeString;
			}
			return date_i18n( $sFullTimeString, $this->getTimeAsGmtOffset( $nTime ) );
		}

		/**
		 * @return string
		 */
		public function getTimeAsGmtOffset( $nTime = null ) {

			$nTimezoneOffset = wp_timezone_override_offset();
			if ( $nTimezoneOffset === false ) {
				$nTimezoneOffset = $this->getOption( 'gmt_offset' );
				if ( empty( $nTimezoneOffset ) ) {
					$nTimezoneOffset = 0;
				}
			}

			$nTime = empty( $nTime ) ? $this->loadDataProcessor()->time() : $nTime;
			return $nTime + ( $nTimezoneOffset * HOUR_IN_SECONDS );
		}

		/**
		 * @return string
		 */
		public function getTimeFormat() {
			$sFormat = $this->getOption( 'time_format' );
			if ( empty( $sFormat ) ) {
				$sFormat = 'H:i';
			}
			return $sFormat;
		}

		/**
		 * @return string
		 */
		public function getDateFormat() {
			$sFormat = $this->getOption( 'date_format' );
			if ( empty( $sFormat ) ) {
				$sFormat = 'F j, Y';
			}
			return $sFormat;
		}

		/**
		 * @return false|WP_Automatic_Updater
		 */
		public function getWpAutomaticUpdater() {
			if ( !isset( $this->oWpAutomaticUpdater ) ) {
				if ( !class_exists( 'WP_Automatic_Updater', false ) ) {
					include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
				}
				if ( class_exists( 'WP_Automatic_Updater', false ) ) {
					$this->oWpAutomaticUpdater = new WP_Automatic_Updater();
				}
				else {
					$this->oWpAutomaticUpdater = false;
				}
			}
			return $this->oWpAutomaticUpdater;
		}

		/**
		 * Flushes the Rewrite rules and forces a re-commit to the .htaccess where applicable
		 */
		public function resavePermalinks() {
			/** @var WP_Rewrite $wp_rewrite */
			global $wp_rewrite;
			if ( is_object( $wp_rewrite ) ) {
				$wp_rewrite->flush_rules( true );
			}
		}

		/**
		 * @return bool
		 */
		public function turnOffCache() {
			if ( !defined( 'DONOTCACHEPAGE' ) ) {
				define( 'DONOTCACHEPAGE', true );
			}
			// WP Fastest Cache
			if ( isset( $GLOBALS[ 'wp_fastest_cache' ] ) and is_object( $GLOBALS[ 'wp_fastest_cache' ] )
				&& method_exists( $GLOBALS[ 'wp_fastest_cache' ], 'deleteCache' )
				&& is_callable( array( $GLOBALS[ 'wp_fastest_cache' ], 'deleteCache' ) )
			) {
				$GLOBALS[ 'wp_fastest_cache' ]->deleteCache(); //WpFastestCache
			}
			return DONOTCACHEPAGE;
		}

		/**
		 * @param string $sMessage
		 * @param string $sTitle
		 * @param bool $bTurnOffCachePage
		 */
		public function wpDie( $sMessage, $sTitle = '', $bTurnOffCachePage = true ) {
			if ( $bTurnOffCachePage ) {
				$this->turnOffCache();
			}
			wp_die( $sMessage, $sTitle );
		}

		/** DEPRECATED: */

		/**
		 * @deprecated
		 * @param array $aLoginUrlParams
		 */
		public function forceUserRelogin( $aLoginUrlParams = array() ) {
			$this->loadWpUsers()->forceUserRelogin( $aLoginUrlParams );
		}

		/**
		 * @deprecated
		 * @return null|WP_User
		 */
		public function getCurrentWpUser() {
			return $this->loadWpUsers()->getCurrentWpUser();
		}

		/**
		 * @deprecated
		 * @return integer
		 */
		public function getCurrentUserLevel() {
			return $this->loadWpUsers()->getCurrentUserLevel();
		}

		/**
		 * @deprecated
		 * @param int $nId
		 * @return WP_User|null
		 */
		public function getUserById( $nId ) {
			return $this->loadWpUsers()->getUserById( $nId );
		}

		/**
		 * @deprecated
		 * @param $sUsername
		 * @return bool|WP_User
		 */
		public function getUserByUsername( $sUsername ) {
			return $this->loadWpUsers()->getUserByUsername( $sUsername );
		}

		/**
		 * @deprecated
		 * @param string $sKey should be already prefixed
		 * @param int|null $nId - if omitted get for current user
		 * @return bool|string
		 */
		public function getUserMeta( $sKey, $nId = null ) {
			return $this->loadWpUsers()->getUserMeta( $sKey, $nId );
		}

		/**
		 * Updates the user meta data for the current (or supplied user ID)
		 *
		 * @deprecated
		 * @param string $sKey
		 * @param mixed $mValue
		 * @param integer $nId		-user ID
		 * @return boolean
		 */
		public function updateUserMeta( $sKey, $mValue, $nId = null ) {
			return $this->loadWpUsers()->updateUserMeta( $sKey, $mValue, $nId );
		}

		/**
		 * @deprecated
		 * @param string $sUsername
		 * @return bool
		 */
		public function setUserLoggedIn( $sUsername ) {
			return $this->loadWpUsers()->setUserLoggedIn( $sUsername );
		}

		/**
		 * @deprecated
		 * @return bool
		 */
		public function comments_getIsCommentPost() {
			return $this->loadWpCommentsProcessor()->isCommentPost();
		}

		/**
		 * @deprecated
		 * @param string $sAuthorEmail
		 * @return bool
		 */
		public function comments_getIfCommentAuthorPreviouslyApproved( $sAuthorEmail ) {
			return $this->loadWpCommentsProcessor()->isCommentAuthorPreviouslyApproved( $sAuthorEmail );
		}

		/**
		 * @deprecated
		 * @param WP_Post $oPost
		 * @return bool
		 */
		public function comments_getIfCommentsOpen( $oPost = null ) {
			return $this->loadWpCommentsProcessor()->isCommentsOpen( $oPost );
		}
	}
endif;