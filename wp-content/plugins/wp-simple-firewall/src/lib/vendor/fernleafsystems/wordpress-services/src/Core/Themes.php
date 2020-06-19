<?php

namespace FernleafSystems\Wordpress\Services\Core;

use FernleafSystems\Wordpress\Services\Core\Upgrades;
use FernleafSystems\Wordpress\Services\Core\VOs\WpThemeVo;
use FernleafSystems\Wordpress\Services\Services;
use FernleafSystems\Wordpress\Services\Utilities\WpOrg\Theme\Api;

/**
 * Class Themes
 * @package FernleafSystems\Wordpress\Services\Core
 */
class Themes {

	/**
	 * @var WpThemeVo[]
	 */
	private $aLoadedVOs;

	/**
	 * @param string $sThemeStylesheet
	 * @return bool
	 */
	public function activate( $sThemeStylesheet ) {
		if ( empty( $sThemeStylesheet ) ) {
			return false;
		}

		$oTheme = $this->getTheme( $sThemeStylesheet );
		if ( !$oTheme->exists() ) {
			return false;
		}

		switch_theme( $oTheme->get_stylesheet() );

		// Now test currently active theme
		$oCurrentTheme = $this->getCurrent();

		return ( $sThemeStylesheet == $oCurrentTheme->get_stylesheet() );
	}

	/**
	 * @param string $sStylesheet
	 * @return bool|\WP_Error
	 */
	public function delete( $sStylesheet ) {
		if ( empty( $sStylesheet ) ) {
			return false;
		}
		if ( !function_exists( 'delete_theme' ) ) {
			require_once( ABSPATH.'wp-admin/includes/theme.php' );
		}
		return function_exists( 'delete_theme' ) ? delete_theme( $sStylesheet ) : false;
	}

	/**
	 * @param string $sSlug
	 * @return bool
	 */
	public function installFromWpOrg( $sSlug ) {
		$bSuccess = false;
		try {
			$oTheme = ( new Api() )
				->setWorkingSlug( $sSlug )
				->getInfo();
			if ( !empty( $oTheme->download_link ) ) {
				$bSuccess = $this->install( $oTheme->download_link, true )[ 'successful' ];
			}
		}
		catch ( \Exception $oE ) {
		}
		return $bSuccess;
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
		$oUpgrader = new \Theme_Upgrader( $oSkin );
		add_filter( 'upgrader_package_options', function ( $aOptions ) use ( $bOverwrite ) {
			$aOptions[ 'clear_destination' ] = $bOverwrite;
			return $aOptions;
		} );

		$mResult = $oUpgrader->install( $sUrlToInstall );

		return [
			'successful' => $mResult === true,
			'feedback'   => $oSkin->getIcwpFeedback(),
			'theme_info' => $oUpgrader->theme_info(),
			'errors'     => is_wp_error( $mResult ) ? $mResult->get_error_messages() : [ 'no errors' ]
		];
	}

	/**
	 * @param string $sSlug
	 * @param bool   $bUseBackup
	 * @return bool
	 */
	public function reinstall( $sSlug, $bUseBackup = false ) {
		$bSuccess = false;

		if ( $this->isInstalled( $sSlug ) ) {
			$oFS = Services::WpFs();

			$oTheme = $this->getTheme( $sSlug );

			$sDir = $oTheme->get_stylesheet_directory();
			$sBackupDir = dirname( $sDir ).'/../../'.$sSlug.'bak'.time();
			if ( $bUseBackup ) {
				rename( $sDir, $sBackupDir );
			}

			$bSuccess = $this->installFromWpOrg( $sSlug );
			if ( $bSuccess ) {
				wp_update_themes(); //refreshes our update information
				if ( $bUseBackup ) {
					$oFS->deleteDir( $sBackupDir );
				}
			}
			elseif ( $bUseBackup ) {
				$oFS->deleteDir( $sDir );
				rename( $sBackupDir, $sDir );
			}
		}
		return $bSuccess;
	}

	/**
	 * @param string $sFile
	 * @return array
	 */
	public function update( $sFile ) {
		require_once( ABSPATH.'wp-admin/includes/upgrade.php' );
		require_once( ABSPATH.'wp-admin/includes/class-wp-upgrader.php' );

		$oSkin = new \Automatic_Upgrader_Skin();
		$mResult = ( new \Theme_Upgrader( $oSkin ) )->upgrade( $sFile );

		return [
			'successful' => $mResult === true,
			'feedback'   => $oSkin->get_upgrade_messages(),
			'errors'     => is_wp_error( $mResult ) ? $mResult->get_error_messages() : [ 'no errors' ]
		];
	}

	/**
	 * @return false|string
	 */
	public function getCurrentThemeName() {
		return $this->getCurrent()->get( 'Name' );
	}

	/**
	 * @return null|\WP_Theme
	 */
	public function getCurrent() {
		return $this->getTheme();
	}

	/**
	 * @param string $sStylesheet
	 * @return bool
	 */
	public function getExists( $sStylesheet ) {
		return $this->getTheme( $sStylesheet )->exists();
	}

	/**
	 * @param string $sSlug - the folder name of the theme
	 * @return string
	 */
	public function getInstallationDir( $sSlug ) {
		return wp_normalize_path( $this->getTheme( $sSlug )->get_stylesheet_directory() );
	}

	/**
	 * Supports only WP > 3.4.0
	 * @param string $sStylesheet
	 * @return null|\WP_Theme
	 */
	public function getTheme( $sStylesheet = null ) {
		require_once( ABSPATH.'wp-admin/includes/theme.php' );
		return function_exists( 'wp_get_theme' ) ? wp_get_theme( $sStylesheet ) : null;
	}

	/**
	 * @param string $sStylesheet
	 * @param bool   $bReload
	 * @return WpThemeVo|null
	 */
	public function getThemeAsVo( $sStylesheet, $bReload = false ) {
		try {
			if ( !is_array( $this->aLoadedVOs ) ) {
				$this->aLoadedVOs = [];
			}
			if ( $bReload || !isset( $this->aLoadedVOs[ $sStylesheet ] ) ) {
				$this->aLoadedVOs[ $sStylesheet ] = new WpThemeVo( $sStylesheet );
			}
			$oAsset = $this->aLoadedVOs[ $sStylesheet ];
		}
		catch ( \Exception $oE ) {
			$oAsset = null;
		}
		return $oAsset;
	}

	/**
	 * @return WpThemeVo[]
	 */
	public function getThemesAsVo() {
		return array_filter(
			array_map(
				function ( $sStyleSheet ) {
					return $this->getThemeAsVo( $sStyleSheet );
				},
				$this->getInstalledStylesheets()
			)
		);
	}

	/**
	 * @return string[]
	 */
	public function getInstalledStylesheets() {
		return array_map(
			function ( $oTheme ) {
				/** @var \WP_Theme $oTheme */
				return $oTheme->get_stylesheet();
			},
			$this->getThemes()
		);
	}

	/**
	 * Supports only WP > 3.4.0
	 * Abstracts the WordPress wp_get_themes()
	 * @return \WP_Theme[]
	 */
	public function getThemes() {
		require_once( ABSPATH.'wp-admin/includes/theme.php' );
		return function_exists( 'wp_get_themes' ) ? wp_get_themes() : [];
	}

	/**
	 * @param string $sSlug
	 * @return array|null
	 */
	public function getUpdateInfo( $sSlug ) {
		$aU = $this->getUpdates();
		return isset( $aU[ $sSlug ] ) ? $aU[ $sSlug ] : null;
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
		$aUpdates = Services::WpGeneral()->getWordpressUpdates( 'themes' );
		return is_array( $aUpdates ) ? $aUpdates : [];
	}

	/**
	 * @return null|\WP_Theme
	 */
	public function getCurrentParent() {
		return $this->isActiveThemeAChild() ? $this->getTheme( get_template() ) : null;
	}

	/**
	 * @return array[] - keys are theme stylesheets
	 */
	public function getAllExtendedData() {
		$oData = Services::WpGeneral()->getTransient( 'update_themes' );
		return array_merge(
			isset( $oData->no_update ) ? $oData->no_update : [],
			isset( $oData->response ) ? $oData->response : []
		);
	}

	/**
	 * @param string $sSlug
	 * @return array
	 */
	public function getExtendedData( $sSlug ) {
		$aData = $this->getAllExtendedData();
		return isset( $aData[ $sSlug ] ) ? $aData[ $sSlug ] : [];
	}

	/**
	 * @param string $sSlug
	 * @param bool   $bCheckIsActiveParent
	 * @return bool
	 */
	public function isActive( $sSlug, $bCheckIsActiveParent = false ) {
		return ( $this->isInstalled( $sSlug ) && $this->getCurrent()->get_stylesheet() == $sSlug )
			   || ( $bCheckIsActiveParent && $this->isActiveParent( $sSlug ) );
	}

	/**
	 * @return bool
	 */
	public function isActiveThemeAChild() {
		$oTheme = $this->getCurrent();
		return ( $oTheme->get_stylesheet() != $oTheme->get_template() );
	}

	/**
	 * @param string $sSlug
	 * @return bool - true if this is the Parent of the currently active theme
	 */
	public function isActiveParent( $sSlug ) {
		return ( $this->isInstalled( $sSlug ) && $this->getCurrent()->get_template() == $sSlug );
	}

	/**
	 * @param string $sSlug The directory slug.
	 * @return bool
	 */
	public function isInstalled( $sSlug ) {
		return ( !empty( $sSlug ) && $this->getExists( $sSlug ) );
	}

	/**
	 * @param string $sSlug
	 * @return bool
	 */
	public function isUpdateAvailable( $sSlug ) {
		return !is_null( $this->getUpdateInfo( $sSlug ) );
	}

	/**
	 * @param string $sBaseName
	 * @return bool
	 */
	public function isWpOrg( $sBaseName ) {
		return $this->getThemeAsVo( $sBaseName )->isWpOrg();
	}

	/**
	 * @return bool|null
	 */
	protected function checkForUpdates() {

		if ( class_exists( 'WPRC_Installer' ) && method_exists( 'WPRC_Installer', 'wprc_update_themes' ) ) {
			\WPRC_Installer::wprc_update_themes();
			return true;
		}
		elseif ( function_exists( 'wp_update_themes' ) ) {
			return ( wp_update_themes() !== false );
		}
		return null;
	}

	/**
	 */
	protected function clearUpdates() {
		$sKey = 'update_themes';
		$oResponse = Services::WpGeneral()->getTransient( $sKey );
		if ( !is_object( $oResponse ) ) {
			$oResponse = new \stdClass();
		}
		$oResponse->last_checked = 0;
		Services::WpGeneral()->setTransient( $sKey, $oResponse );
	}

	/**
	 * @return array
	 */
	public function wpmsGetSiteAllowedThemes() {
		return ( function_exists( 'get_site_allowed_themes' ) ? get_site_allowed_themes() : [] );
	}
}