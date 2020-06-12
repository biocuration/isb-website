<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Theme;

use FernleafSystems\Wordpress\Services;

/**
 * Class Files
 * @package FernleafSystems\Wordpress\Services\Utilities\WpOrg\Theme
 */
class Files extends Services\Utilities\WpOrg\Base\PluginThemeFilesBase {

	use Base;

	/**
	 * Given a full root path on the file system for a file, locate the plugin to which this file belongs.
	 * @param string $sFullFilePath
	 * @return Services\Core\VOs\WpThemeVo|null
	 */
	public function findThemeFromFile( $sFullFilePath ) {
		$oTheTheme = null;

		$sFragment = $this->getThemePathFragmentFromPath( $sFullFilePath );

		if ( !empty( $sFragment ) && strpos( $sFragment, '/' ) > 0 ) {
			$oWpThemes = Services\Services::WpThemes();
			$sDir = substr( $sFragment, 0, strpos( $sFragment, '/' ) );
			foreach ( $oWpThemes->getThemes() as $oTheme ) {
				if ( $sDir == $oTheme->get_stylesheet() ) {
					$oTheTheme = $oWpThemes->getThemeAsVo( $sDir );
					break;
				}
			}
		}
		return $oTheTheme;
	}

	/**
	 * Verifies the file exists on the SVN repository for the particular version that's installed.
	 * @param string $sFullFilePath
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public function isValidFileFromTheme( $sFullFilePath ) {

		$oTheTheme = $this->findThemeFromFile( $sFullFilePath );
		if ( !$oTheTheme instanceof Services\Core\VOs\WpThemeVo ) {
			throw new \InvalidArgumentException( 'Not actually a theme file.', 1 );
		}
		if ( !$oTheTheme->isWpOrg() ) {
			throw new \InvalidArgumentException( 'Not a WordPress.org theme.', 2 );
		}

		// if uses SVN tags, use that version. Otherwise trunk.
		return ( new Repo() )
			->setWorkingSlug( $oTheTheme->stylesheet )
			->setWorkingVersion( $oTheTheme->version )
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
	 * Verifies the file exists on the SVN repository for the particular version that's installed.
	 * @param string $sFullFilePath
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public function verifyFileContents( $sFullFilePath ) {
		$sTmpFile = $this->getOriginalFileFromVcs( $sFullFilePath );
		return !empty( $sTmpFile )
			   && ( new Services\Utilities\File\Compare\CompareHash() )
				   ->isEqualFilesMd5( $sTmpFile, $sFullFilePath );
	}

	/**
	 * @param string $sFullFilePath
	 * @return string|null
	 */
	public function getOriginalFileFromVcs( $sFullFilePath ) {
		$sTmpFile = null;
		$oTheTheme = $this->findThemeFromFile( $sFullFilePath );
		if ( $oTheTheme instanceof Services\Core\VOs\WpThemeVo ) {
			$sTmpFile = ( new Repo() )
				->setWorkingSlug( $oTheTheme->stylesheet )
				->setWorkingVersion( $oTheTheme->version )
				->downloadFromVcs( $this->getRelativeFilePathFromItsInstallDir( $sFullFilePath ) );
		}
		return $sTmpFile;
	}

	/**
	 * @param string $sFile - can either be absolute, or relative to ABSPATH
	 * @return string|null - the path to the file relative to Plugins Dir.
	 */
	public function getThemePathFragmentFromPath( $sFile ) {
		$sFragment = null;

		if ( !Services\Services::WpFs()->isAbsPath( $sFile ) ) { // assume it's relative to ABSPATH
			$sFile = path_join( ABSPATH, $sFile );
		}
		$sFile = wp_normalize_path( $sFile );
		$sThemesDir = wp_normalize_path( get_theme_root() );

		if ( strpos( $sFile, $sThemesDir ) === 0 ) {
			$sFragment = ltrim( str_replace( $sThemesDir, '', $sFile ), '/' );
		}

		return $sFragment;
	}

	/**
	 * Gets the path of the plugin file relative to its own home plugin dir. (not wp-content/plugins/)
	 * @param string $sFile
	 * @return string
	 */
	protected function getRelativeFilePathFromItsInstallDir( $sFile ) {
		$sRelDirFragment = $this->getThemePathFragmentFromPath( $sFile );
		return substr( $sRelDirFragment, strpos( $sRelDirFragment, '/' ) + 1 );
	}
}