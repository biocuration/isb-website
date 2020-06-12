<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin;

use FernleafSystems\Wordpress\Services;

/**
 * Class Files
 * @package FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin
 */
class Files extends Services\Utilities\WpOrg\Base\PluginThemeFilesBase {

	use Base;

	/**
	 * Given a full root path on the file system for a file, locate the plugin to which this file belongs.
	 * @param string $sFullFilePath
	 * @return Services\Core\VOs\WpPluginVo|null
	 */
	public function findPluginFromFile( $sFullFilePath ) {
		$oThePlugin = null;

		$sFragment = $this->getPluginPathFragmentFromPath( $sFullFilePath );

		if ( !empty( $sFragment ) && strpos( $sFragment, '/' ) > 0 ) {
			$oWpPlugins = Services\Services::WpPlugins();
			$sDir = substr( $sFragment, 0, strpos( $sFragment, '/' ) );
			foreach ( $oWpPlugins->getInstalledPluginFiles() as $sPluginFile ) {
				if ( $sDir == dirname( $sPluginFile ) ) {
					$oThePlugin = $oWpPlugins->getPluginAsVo( $sPluginFile );
					break;
				}
			}
		}
		return $oThePlugin;
	}

	/**
	 * Verifies the file exists on the SVN repository for the particular version that's installed.
	 * @param string $sFullFilePath
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public function isValidFileFromPlugin( $sFullFilePath ) {

		$oThePlugin = $this->findPluginFromFile( $sFullFilePath );
		if ( !$oThePlugin instanceof Services\Core\VOs\WpPluginVo ) {
			throw new \InvalidArgumentException( 'Not actually a plugin file.', 1 );
		}
		if ( !$oThePlugin->isWpOrg() ) {
			throw new \InvalidArgumentException( 'Not a WordPress.org plugin.', 2 );
		}

		// if uses SVN tags, use that version. Otherwise trunk.
		return ( new Repo() )
			->setWorkingSlug( $oThePlugin->slug )
			->setWorkingVersion( ( $oThePlugin->svn_uses_tags ? $oThePlugin->Version : 'trunk' ) )
			->existsInVcs( $this->getRelativeFilePathFromItsInstallDir( $sFullFilePath ) );
	}

	/**
	 * @param string $sFullFilePath
	 * @return bool
	 */
	public function replaceFileFromVcs( $sFullFilePath ) {
		$sTmpFile = $this->getOriginalFileFromVcs( $sFullFilePath );
		return !empty( $sTmpFile ) && Services\Services::WpFs()->move( $sTmpFile, $sFullFilePath );
	}

	/**
	 * @param string $sFullFilePath
	 * @return string|null
	 */
	public function getOriginalFileFromVcs( $sFullFilePath ) {
		$sTmpFile = null;
		$oThePlugin = $this->findPluginFromFile( $sFullFilePath );
		if ( $oThePlugin instanceof Services\Core\VOs\WpPluginVo ) {
			$sTmpFile = ( new Repo() )
				->setWorkingSlug( $oThePlugin->slug )
				->setWorkingVersion( ( $oThePlugin->svn_uses_tags ? $oThePlugin->Version : 'trunk' ) )
				->downloadFromVcs( $this->getRelativeFilePathFromItsInstallDir( $sFullFilePath ) );
		}
		return $sTmpFile;
	}

	/**
	 * @param string $sFile - can either be absolute, or relative to ABSPATH
	 * @return string|null - the path to the file relative to Plugins Dir.
	 */
	public function getPluginPathFragmentFromPath( $sFile ) {
		$sFragment = null;

		if ( !Services\Services::WpFs()->isAbsPath( $sFile ) ) { // assume it's relative to ABSPATH
			$sFile = path_join( ABSPATH, $sFile );
		}
		$sFile = wp_normalize_path( $sFile );
		$sPluginsDir = wp_normalize_path( WP_PLUGIN_DIR );

		if ( strpos( $sFile, $sPluginsDir ) === 0 ) {
			$sFragment = ltrim( str_replace( $sPluginsDir, '', $sFile ), '/' );
		}

		return $sFragment;
	}

	/**
	 * Gets the path of the plugin file relative to its own home plugin dir. (not wp-content/plugins/)
	 * @param string $sFile
	 * @return string
	 */
	protected function getRelativeFilePathFromItsInstallDir( $sFile ) {
		$sRelDirFragment = $this->getPluginPathFragmentFromPath( $sFile );
		return substr( $sRelDirFragment, strpos( $sRelDirFragment, '/' ) + 1 );
	}
}