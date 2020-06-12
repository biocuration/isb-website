<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin;

use FernleafSystems\Wordpress\Services;

/**
 * Class Repo
 * @package FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin
 */
class Repo extends Services\Utilities\WpOrg\Base\RepoBase {

	use Base;
	const URL_VCS_ROOT = 'https://plugins.svn.wordpress.org';
	const URL_VCS_DOWNLOAD_VERSIONS = 'https://plugins.svn.wordpress.org/%s/tags/';
	const URL_DOWNLOAD_SVN_FILE = 'https://plugins.svn.wordpress.org/%s/tags/%s/%s';

	/**
	 * @param string $sSlug
	 * @return string
	 */
	public static function GetUrlForPlugin( $sSlug ) {
		return sprintf( '%s/%s', static::URL_VCS_ROOT, $sSlug );
	}

	/**
	 * @param string $sSlug
	 * @param string $sVersion
	 * @return string
	 */
	public static function GetUrlForPluginVersion( $sSlug, $sVersion ) {
		if ( $sVersion != 'trunk' ) {
			$sVersion = sprintf( 'tags/%s', $sVersion );
		}
		return sprintf( '%s/%s', static::GetUrlForPlugin( $sSlug ), $sVersion );
	}

	/**
	 * @param string $sSlug
	 * @return string
	 */
	public static function GetUrlForPluginVersions( $sSlug ) {
		return static::GetUrlForPluginVersion( $sSlug, '' );
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
			throw new \InvalidArgumentException( 'Plugin file fragment path provided is empty' );
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
			rtrim( static::GetUrlForPluginVersion( $this->getWorkingSlug(), $sVersion ), '/' ),
			ltrim( $sFileFragment, '/' )
		);
	}
}