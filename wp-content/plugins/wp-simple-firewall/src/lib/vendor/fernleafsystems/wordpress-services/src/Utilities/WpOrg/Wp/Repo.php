<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Wp;

use FernleafSystems\Wordpress\Services;

/**
 * Class Repo
 * @package FernleafSystems\Wordpress\Services\Utilities\WpOrg\Wp
 */
class Repo extends Services\Utilities\WpOrg\Base\RepoBase {

	const URL_VCS_ROOT = 'https://core.svn.wordpress.org';
	const URL_VCS_ROOT_IL8N = 'https://i18n.svn.wordpress.org';

	/**
	 * @param string $sVersion
	 * @param bool   $bUseLocale
	 * @return string
	 */
	public static function GetUrlForVersion( $sVersion, $bUseLocale = true ) {
		return sprintf(
			'%s/tags/%s',
			$bUseLocale ? static::URL_VCS_ROOT_IL8N : static::URL_VCS_ROOT,
			$bUseLocale ? $sVersion.'/dist' : $sVersion
		);
	}

	/**
	 * @return string
	 */
	public static function GetUrlForVersions() {
		return static::GetUrlForVersion( '' );
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
	 * @param bool   $bUseSiteLocale
	 * @return string
	 */
	protected function getVcsUrlForFileAndVersion( $sFileFragment, $sVersion, $bUseSiteLocale = true ) {
		if ( empty( $sVersion ) ) {
			$sVersion = Services\Services::WpGeneral()->getVersion();
		}
		return sprintf( '%s/%s', static::GetUrlForVersion( $sVersion, $bUseSiteLocale ), ltrim( $sFileFragment, '/' ) );
	}
}