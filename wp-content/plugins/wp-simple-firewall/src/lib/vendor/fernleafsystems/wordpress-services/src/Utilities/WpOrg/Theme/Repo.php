<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Theme;

use FernleafSystems\Wordpress\Services;

/**
 * Class Repo
 * @package FernleafSystems\Wordpress\Services\Utilities\WpOrg\Theme
 */
class Repo extends Services\Utilities\WpOrg\Base\RepoBase {

	use Base;
	const URL_VCS_ROOT = 'https://themes.svn.wordpress.org';

	/**
	 * @param string $sSlug
	 * @return string
	 */
	public static function GetUrlForTheme( $sSlug ) {
		return sprintf( '%s/%s', static::URL_VCS_ROOT, $sSlug );
	}

	/**
	 * @param string $sSlug
	 * @param string $sVersion
	 * @return string
	 */
	public static function GetUrlForThemeVersion( $sSlug, $sVersion ) {
		return sprintf( '%s/%s', static::GetUrlForTheme( $sSlug ), $sVersion );
	}

	/**
	 * @param string $sSlug
	 * @return string
	 */
	public static function GetUrlForThemeVersions( $sSlug ) {
		return static::GetUrlForThemeVersion( $sSlug, '' );
	}

	/**
	 * @param string $sFileFragment  - relative to the working plugin directory
	 * @param string $sVersion
	 * @param bool   $bUseSiteLocale - unused
	 * @return string
	 * @throws \Exception
	 */
	protected function getVcsUrlForFileAndVersion( $sFileFragment, $sVersion = null, $bUseSiteLocale = true ) {
		if ( empty( $sFileFragment ) ) {
			throw new \InvalidArgumentException( 'Theme file fragment path provided is empty' );
		}
		if ( empty( $sVersion ) ) {
			$sVersion = $this->getWorkingVersion();
		}
		if ( empty( $sVersion ) ) {
			$sVersion = ( new Versions() )
				->setWorkingSlug( $this->getWorkingSlug() )
				->latest();
		}
		return sprintf( '%s/%s',
			static::GetUrlForThemeVersion( $this->getWorkingSlug(), $sVersion ), ltrim( $sFileFragment, '/' ) );
	}
}