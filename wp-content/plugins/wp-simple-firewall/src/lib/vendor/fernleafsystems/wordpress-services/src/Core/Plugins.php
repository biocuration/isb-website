<?php

namespace FernleafSystems\Wordpress\Services\Core;

use FernleafSystems\Wordpress\Services\Core\Upgrades;
use FernleafSystems\Wordpress\Services\Core\VOs\WpPluginVo;
use FernleafSystems\Wordpress\Services\Services;

/**
 * Class Plugins
 * @package FernleafSystems\Wordpress\Services\Core
 */
class Plugins {

	/**
	 * @var WpPluginVo[]
	 */
	private $aLoadedVOs;

	/**
	 * @param string $sPluginFile
	 * @param bool   $bNetworkWide
	 * @return null|\WP_Error
	 */
	public function activate( $sPluginFile, $bNetworkWide = false ) {
		return activate_plugin( $sPluginFile, '', $bNetworkWide );
	}

	/**
	 * @param string $sPluginFile
	 * @param bool   $bNetworkWide
	 * @return null|\WP_Error
	 */
	protected function activateQuietly( $sPluginFile, $bNetworkWide = false ) {
		return activate_plugin( $sPluginFile, '', $bNetworkWide, true );
	}

	/**
	 * @param string $sPluginFile
	 * @param bool   $bNetworkWide
	 */
	public function deactivate( $sPluginFile, $bNetworkWide = false ) {
		deactivate_plugins( $sPluginFile, '', $bNetworkWide );
	}

	/**
	 * @param string $sPluginFile
	 * @param bool   $bNetworkWide
	 */
	protected function deactivateQuietly( $sPluginFile, $bNetworkWide = false ) {
		deactivate_plugins( $sPluginFile, true, $bNetworkWide );
	}

	/**
	 * @param string $sFile
	 * @param bool   $bNetworkWide
	 * @return bool
	 */
	public function delete( $sFile, $bNetworkWide = false ) {
		if ( !$this->isInstalled( $sFile ) ) {
			return false;
		}

		if ( $this->isActive( $sFile ) ) {
			$this->deactivate( $sFile, $bNetworkWide );
		}
		$this->uninstall( $sFile );

		// delete the folder
		$sPluginDir = dirname( $sFile );
		if ( $sPluginDir == '.' ) { //it's not within a sub-folder
			$sPluginDir = $sFile;
		}
		$sPath = path_join( WP_PLUGIN_DIR, $sPluginDir );
		return Services::WpFs()->deleteDir( $sPath );
	}

	/**
	 * @param string $sUrlToInstall
	 * @param bool   $bOverwrite
	 * @return array
	 */
	public function install( $sUrlToInstall, $bOverwrite = true ) {

		$oSkin = Services::WpGeneral()->getWordpressIsAtLeastVersion( '5.3' ) ?
			new Upgrades\UpgraderSkin()
			: new Upgrades\UpgraderSkinLegacy();
		$oUpgrader = new \Plugin_Upgrader( $oSkin );
		add_filter( 'upgrader_package_options', function ( $aOptions ) use ( $bOverwrite ) {
			$aOptions[ 'clear_destination' ] = $bOverwrite;
			return $aOptions;
		} );

		$mResult = $oUpgrader->install( $sUrlToInstall );

		return [
			'successful'  => $mResult === true,
			'feedback'    => $oSkin->getIcwpFeedback(),
			'plugin_info' => $oUpgrader->plugin_info(),
			'errors'      => is_wp_error( $mResult ) ? $mResult->get_error_messages() : [ 'no errors' ]
		];
	}

	/**
	 * @param $sSlug
	 * @return array|bool
	 */
	public function installFromWpOrg( $sSlug ) {
		include_once( ABSPATH.'wp-admin/includes/plugin-install.php' );

		$api = plugins_api( 'plugin_information', [
			'slug'   => $sSlug,
			'fields' => [
				'sections' => false,
			],
		] );

		if ( !is_wp_error( $api ) ) {
			return $this->install( $api->download_link, true );
		}
		return false;
	}

	/**
	 * @param string $sFile
	 * @param bool   $bUseBackup
	 * @return bool
	 */
	public function reinstall( $sFile, $bUseBackup = false ) {
		$bSuccess = false;

		if ( $this->isInstalled( $sFile ) ) {

			$sSlug = $this->getSlug( $sFile );
			if ( !empty( $sSlug ) ) {
				$oFS = Services::WpFs();

				$sDir = dirname( path_join( WP_PLUGIN_DIR, $sFile ) );
				$sBackupDir = WP_PLUGIN_DIR.'/../'.basename( $sDir ).'bak'.time();
				if ( $bUseBackup ) {
					rename( $sDir, $sBackupDir );
				}

				$aResult = $this->installFromWpOrg( $sSlug );
				$bSuccess = $aResult[ 'successful' ];
				if ( $bSuccess ) {
					wp_update_plugins(); //refreshes our update information
					if ( $bUseBackup ) {
						$oFS->deleteDir( $sBackupDir );
					}
				}
				elseif ( $bUseBackup ) {
					$oFS->deleteDir( $sDir );
					rename( $sBackupDir, $sDir );
				}
			}
		}
		return $bSuccess;
	}

	/**
	 * @param string $sFile
	 * @return array
	 */
	public function update( $sFile ) {
		require_once( ABSPATH.'wp-admin/includes/class-wp-upgrader.php' );

		$bWasActive = $this->isActive( $sFile );

		$oSkin = new \Automatic_Upgrader_Skin();
		$mResult = ( new \Plugin_Upgrader( $oSkin ) )->bulk_upgrade( [ $sFile ] );

		$bSuccess = false;
		if ( is_array( $mResult ) && isset( $mResult[ $sFile ] ) ) {
			$mResult = array_shift( $mResult );
			$bSuccess = !empty( $mResult ) && is_array( $mResult );
		}

		if ( $bWasActive && !$this->isActive( $sFile ) ) {
			$this->activate( $sFile );
		}

		return [
			'successful' => $bSuccess,
			'feedback'   => $oSkin->get_upgrade_messages(),
			'errors'     => is_wp_error( $mResult ) ? $mResult->get_error_messages() : [ 'no errors' ]
		];
	}

	/**
	 * @param string $sPluginFile
	 * @return true
	 */
	public function uninstall( $sPluginFile ) {
		return uninstall_plugin( $sPluginFile );
	}

	/**
	 * @return bool|null
	 */
	protected function checkForUpdates() {

		if ( class_exists( 'WPRC_Installer' ) && method_exists( 'WPRC_Installer', 'wprc_update_plugins' ) ) {
			\WPRC_Installer::wprc_update_plugins();
			return true;
		}
		elseif ( function_exists( 'wp_update_plugins' ) ) {
			return ( wp_update_plugins() !== false );
		}
		return null;
	}

	/**
	 */
	protected function clearUpdates() {
		$oWp = Services::WpGeneral();
		$sKey = 'update_plugins';
		$oResponse = Services::WpGeneral()->getTransient( $sKey );
		if ( !is_object( $oResponse ) ) {
			$oResponse = new \stdClass();
		}
		$oResponse->last_checked = 0;
		$oWp->setTransient( $sKey, $oResponse );
	}

	/**
	 * @param string $sValueToCompare
	 * @param string $sKey
	 * @return null|string
	 */
	public function findPluginBy( $sValueToCompare, $sKey = 'Name' ) {
		$sFilename = null;

		if ( !empty( $sValueToCompare ) ) {
			foreach ( $this->getPlugins() as $sBaseFileName => $aPluginData ) {
				if ( isset( $aPluginData[ $sKey ] ) && $sValueToCompare == $aPluginData[ $sKey ] ) {
					$sFilename = $sBaseFileName;
				}
			}
		}

		return $sFilename;
	}

	/**
	 * @param string $sDirName
	 * @return string|null
	 */
	public function findPluginFileFromDirName( $sDirName ) {
		$sFile = null;
		if ( !empty( $sDirName ) ) {
			foreach ( $this->getInstalledBaseFiles() as $sF ) {
				if ( strpos( $sF, $sDirName.'/' ) === 0 ) {
					$sFile = $sF;
					break;
				}
			}
		}
		return $sFile;
	}

	/**
	 * @param string $sFile - plugin base file, e.g. wp-folder/wp-plugin.php
	 * @return string
	 */
	public function getInstallationDir( $sFile ) {
		return wp_normalize_path( dirname( path_join( WP_PLUGIN_DIR, $sFile ) ) );
	}

	/**
	 * @param string $sPluginFile
	 * @return string
	 */
	public function getLinkPluginActivate( $sPluginFile ) {
		$sUrl = self_admin_url( 'plugins.php' );
		$aQueryArgs = [
			'action'   => 'activate',
			'plugin'   => urlencode( $sPluginFile ),
			'_wpnonce' => wp_create_nonce( 'activate-plugin_'.$sPluginFile )
		];
		return add_query_arg( $aQueryArgs, $sUrl );
	}

	/**
	 * @param string $sPluginFile
	 * @return string
	 */
	public function getLinkPluginDeactivate( $sPluginFile ) {
		$sUrl = self_admin_url( 'plugins.php' );
		$aQueryArgs = [
			'action'   => 'deactivate',
			'plugin'   => urlencode( $sPluginFile ),
			'_wpnonce' => wp_create_nonce( 'deactivate-plugin_'.$sPluginFile )
		];
		return add_query_arg( $aQueryArgs, $sUrl );
	}

	/**
	 * @param string $sPluginFile
	 * @return string
	 */
	public function getLinkPluginUpgrade( $sPluginFile ) {
		$sUrl = self_admin_url( 'update.php' );
		$aQueryArgs = [
			'action'   => 'upgrade-plugin',
			'plugin'   => urlencode( $sPluginFile ),
			'_wpnonce' => wp_create_nonce( 'upgrade-plugin_'.$sPluginFile )
		];
		return add_query_arg( $aQueryArgs, $sUrl );
	}

	/**
	 * @param string $sPluginFile
	 * @return array|null
	 */
	public function getPlugin( $sPluginFile ) {
		return $this->isInstalled( $sPluginFile ) ? $this->getPlugins()[ $sPluginFile ] : null;
	}

	/**
	 * @param string $sPluginFile
	 * @param bool   $bReload
	 * @return WpPluginVo|null
	 */
	public function getPluginAsVo( $sPluginFile, $bReload = false ) {
		try {
			if ( !is_array( $this->aLoadedVOs ) ) {
				$this->aLoadedVOs = [];
			}
			if ( $bReload || !isset( $this->aLoadedVOs[ $sPluginFile ] ) ) {
				$this->aLoadedVOs[ $sPluginFile ] = new WpPluginVo( $sPluginFile );
			}
			$oAsset = $this->aLoadedVOs[ $sPluginFile ];
		}
		catch ( \Exception $oE ) {
			$oAsset = null;
		}
		return $oAsset;
	}

	/**
	 * @param string $sPluginFile
	 * @return null|\stdClass
	 */
	public function getPluginDataAsObject( $sPluginFile ) {
		$aPlugin = $this->getPlugin( $sPluginFile );
		return is_null( $aPlugin ) ? null : Services::DataManipulation()->convertArrayToStdClass( $aPlugin );
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
		$oWp = Services::WpGeneral();
		$sOptionKey = $oWp->isMultisite() ? 'active_sitewide_plugins' : 'active_plugins';
		return $oWp->getOption( $sOptionKey );
	}

	/**
	 * @return array
	 */
	public function getInstalledBaseFiles() {
		return array_keys( $this->getPlugins() );
	}

	/**
	 * @return string[]
	 */
	public function getInstalledPluginFiles() {
		return array_keys( $this->getPlugins() );
	}

	/**
	 * @return string[]
	 */
	public function getInstalledWpOrgPluginFiles() {
		return array_values( array_filter(
			$this->getInstalledPluginFiles(),
			function ( $sFile ) {
				return $this->isWpOrg( $sFile );
			}
		) );
	}

	/**
	 * @return array[]
	 */
	public function getPlugins() {
		if ( !function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH.'wp-admin/includes/plugin.php' );
		}
		return function_exists( 'get_plugins' ) ? get_plugins() : [];
	}

	/**
	 * @return WpPluginVo[]
	 */
	public function getPluginsAsVo() {
		return array_filter(
			array_map(
				function ( $sPluginFile ) {
					return $this->getPluginAsVo( $sPluginFile );
				},
				$this->getInstalledPluginFiles()
			)
		);
	}

	/**
	 * @return \stdClass[] - keys are plugin base files
	 */
	public function getAllExtendedData() {
		$oData = Services::WpGeneral()->getTransient( 'update_plugins' );
		return array_merge(
			isset( $oData->no_update ) ? $oData->no_update : [],
			isset( $oData->response ) ? $oData->response : []
		);
	}

	/**
	 * @param string $sBaseFile
	 * @return array
	 */
	public function getExtendedData( $sBaseFile ) {
		$aData = $this->getAllExtendedData();
		return isset( $aData[ $sBaseFile ] ) ?
			Services::DataManipulation()->convertStdClassToArray( $aData[ $sBaseFile ] )
			: [];
	}

	/**
	 * @return array
	 */
	public function getAllSlugs() {
		$aSlugs = [];

		foreach ( $this->getAllExtendedData() as $sBaseName => $oPlugData ) {
			if ( isset( $oPlugData->slug ) ) {
				$aSlugs[ $sBaseName ] = $oPlugData->slug;
			}
		}

		return $aSlugs;
	}

	/**
	 * @param $sBaseName
	 * @return string
	 */
	public function getSlug( $sBaseName ) {
		$aInfo = $this->getExtendedData( $sBaseName );
		return isset( $aInfo[ 'slug' ] ) ? $aInfo[ 'slug' ] : '';
	}

	/**
	 * @param string $sBaseName
	 * @return bool
	 * @deprecated 1.1.17
	 */
	public function isWpOrg( $sBaseName ) {
		return $this->getPluginAsVo( $sBaseName )->isWpOrg();
	}

	/**
	 * @param string $sFile
	 * @return \stdClass|null
	 */
	public function getUpdateInfo( $sFile ) {
		$aU = $this->getUpdates();
		return isset( $aU[ $sFile ] ) ? $aU[ $sFile ] : null;
	}

	/**
	 * @param string $sFile
	 * @return string
	 */
	public function getUpdateNewVersion( $sFile ) {
		$oInfo = $this->getUpdateInfo( $sFile );
		return ( !is_null( $oInfo ) && isset( $oInfo->new_version ) ) ? $oInfo->new_version : '';
	}

	/**
	 * @param bool $bForceUpdateCheck
	 * @return array
	 */
	public function getUpdates( $bForceUpdateCheck = false ) {
		if ( $bForceUpdateCheck ) {
			$this->clearUpdates();
			$this->checkForUpdates();
		}
		$aUpdates = Services::WpGeneral()->getWordpressUpdates( 'plugins' );
		return is_array( $aUpdates ) ? $aUpdates : [];
	}

	/**
	 * @param string $sPluginFile
	 * @return string
	 */
	public function getUrl_Activate( $sPluginFile ) {
		return $this->getUrl_Action( $sPluginFile, 'activate' );
	}

	/**
	 * @param string $sPluginFile
	 * @return string
	 */
	public function getUrl_Deactivate( $sPluginFile ) {
		return $this->getUrl_Action( $sPluginFile, 'deactivate' );
	}

	/**
	 * @param string $sPluginFile
	 * @return string
	 */
	public function getUrl_Upgrade( $sPluginFile ) {
		$aQueryArgs = [
			'action'   => 'upgrade-plugin',
			'plugin'   => urlencode( $sPluginFile ),
			'_wpnonce' => wp_create_nonce( 'upgrade-plugin_'.$sPluginFile )
		];
		return add_query_arg( $aQueryArgs, self_admin_url( 'update.php' ) );
	}

	/**
	 * @param string $sPluginFile
	 * @param string $sAction
	 * @return string
	 */
	protected function getUrl_Action( $sPluginFile, $sAction ) {
		return add_query_arg(
			[
				'action'   => $sAction,
				'plugin'   => urlencode( $sPluginFile ),
				'_wpnonce' => wp_create_nonce( $sAction.'-plugin_'.$sPluginFile )
			],
			self_admin_url( 'plugins.php' )
		);
	}

	/**
	 * @param string $sFile
	 * @return bool
	 */
	public function isActive( $sFile ) {
		return $this->isInstalled( $sFile ) && is_plugin_active( $sFile );
	}

	/**
	 * @param string $sFile The full plugin file.
	 * @return bool
	 */
	public function isInstalled( $sFile ) {
		return !empty( $sFile ) && in_array( $sFile, $this->getInstalledBaseFiles() );
	}

	/**
	 * @param string $sBaseFile
	 * @return bool
	 */
	public function isPluginAutomaticallyUpdated( $sBaseFile ) {
		$oUpdater = Services::WpGeneral()->getWpAutomaticUpdater();
		if ( !$oUpdater ) {
			return false;
		}

		// Due to a change in the filter introduced in version 3.8.2
		if ( Services::WpGeneral()->getWordpressIsAtLeastVersion( '3.8.2' ) ) {
			$mPluginItem = new \stdClass();
			$mPluginItem->plugin = $sBaseFile;
		}
		else {
			$mPluginItem = $sBaseFile;
		}

		return $oUpdater->should_update( 'plugin', $mPluginItem, WP_PLUGIN_DIR );
	}

	/**
	 * @param string $sFile
	 * @return bool
	 */
	public function isUpdateAvailable( $sFile ) {
		return !is_null( $this->getUpdateInfo( $sFile ) );
	}

	/**
	 * @param string $sFile
	 * @param int    $nDesiredPosition
	 */
	public function setActivePluginLoadPosition( $sFile, $nDesiredPosition = 0 ) {
		$oWp = Services::WpGeneral();
		$oData = Services::DataManipulation();

		$aActive = $oData->setArrayValueToPosition(
			$oWp->getOption( 'active_plugins' ),
			$sFile,
			$nDesiredPosition
		);
		$oWp->updateOption( 'active_plugins', $aActive );

		if ( $oWp->isMultisite() ) {
			$aActive = $oData
				->setArrayValueToPosition( $oWp->getOption( 'active_sitewide_plugins' ), $sFile, $nDesiredPosition );
			$oWp->updateOption( 'active_sitewide_plugins', $aActive );
		}
	}

	/**
	 * @param string $sFile
	 */
	public function setActivePluginLoadFirst( $sFile ) {
		$this->setActivePluginLoadPosition( $sFile, 0 );
	}

	/**
	 * @param string $sFile
	 */
	public function setActivePluginLoadLast( $sFile ) {
		$this->setActivePluginLoadPosition( $sFile, 1000 );
	}
}