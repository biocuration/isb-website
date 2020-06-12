<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Cp;

use FernleafSystems\Wordpress\Services;

/**
 * Class Repo
 * @package FernleafSystems\Wordpress\Services\Utilities\WpOrg\Cp
 */
class Repo extends Services\Utilities\WpOrg\Base\RepoBase {

	const URL_VCS_ROOT = 'https://raw.githubusercontent.com/ClassicPress/ClassicPress-release';
	const URL_VCS_ROOT_IL8N = self::URL_VCS_ROOT;
	const URL_VCS_VERSIONS = 'https://api.github.com/repos/ClassicPress/ClassicPress-release/releases';
	const URL_VCS_VERSION = 'https://github.com/ClassicPress/ClassicPress-release/releases/tag';

	/**
	 * @param string $sVersion
	 * @return string
	 */
	public static function GetUrlForVersion( $sVersion ) {
		return sprintf( '%s/%s', static::URL_VCS_VERSION, $sVersion );
	}

	/**
	 * @param string $sVersion
	 * @return string
	 */
	public static function GetUrlForFiles( $sVersion ) {
		return sprintf( '%s/%s', static::URL_VCS_ROOT, $sVersion );
	}

	/**
	 * @return string
	 */
	public static function GetUrlForVersions() {
		return static::URL_VCS_VERSIONS;
	}

	/**
	 * @param string $sFileFragment
	 * @param string $sVersion
	 * @param bool   $bUseSiteLocale
	 * @return string|null
	 */
	public function downloadFromVcs( $sFileFragment, $sVersion = null, $bUseSiteLocale = true ) {
		$sFile = parent::downloadFromVcs( $sFileFragment, $sVersion, $bUseSiteLocale );
		if ( $bUseSiteLocale && empty( $sFile ) ) {
			$sFile = parent::downloadFromVcs( $sFileFragment, $sVersion, false );
		}
		return $sFile;
	}

	/**
	 * @param string $sFileFragment
	 * @param string $sVersion - leave empty to use the current version
	 * @param bool   $bUseSiteLocale
	 * @return bool
	 */
	public function existsInVcs( $sFileFragment, $sVersion = null, $bUseSiteLocale = true ) {
		$sFile = parent::existsInVcs( $sFileFragment, $sVersion, $bUseSiteLocale );
		if ( $bUseSiteLocale && empty( $sFile ) ) {
			$sFile = parent::existsInVcs( $sFileFragment, $sVersion, false );
		}
		return $sFile;
	}

	/**
	 * @param string $sFileFragment
	 * @param string $sVersion
	 * @param bool   $bUseSiteLocale - not yet used for ClassicPress
	 * @return string
	 */
	protected function getVcsUrlForFileAndVersion( $sFileFragment, $sVersion, $bUseSiteLocale = true ) {
		if ( empty( $sVersion ) ) {
			$sVersion = Services\Services::WpGeneral()->getVersion();
		}
		return sprintf( '%s/%s', static::GetUrlForFiles( $sVersion ), ltrim( $sFileFragment, '/' ) );
	}
}