<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Base;

use FernleafSystems\Wordpress\Services;

/**
 * Class RepoBase
 * @package FernleafSystems\Wordpress\Services\Utilities\WpOrg\Base
 */
abstract class RepoBase {

	/**
	 * @param string $sFileFragment
	 * @param string $sVersion
	 * @param bool   $bUseSiteLocale
	 * @return string|null
	 */
	public function downloadFromVcs( $sFileFragment, $sVersion = null, $bUseSiteLocale = true ) {
		$sUrl = $this->getVcsUrlForFileAndVersion( $sFileFragment, $sVersion, $bUseSiteLocale );
		try {
			$sTmpFile = ( new Services\Utilities\HttpUtil() )
				->checkUrl( $sUrl )
				->downloadUrl( $sUrl );
		}
		catch ( \Exception $oE ) {
			$sTmpFile = null;
		}
		return $sTmpFile;
	}

	/**
	 * @param string $sFileFragment - path relative to the root dir of the object being tested. E.g. ABSPATH for
	 *                              WordPress or the plugin dir if it's a plugin.
	 * @param string $sVersion      - leave empty to use the current version
	 * @param bool   $bUseSiteLocale
	 * @return bool
	 */
	public function existsInVcs( $sFileFragment, $sVersion = null, $bUseSiteLocale = true ) {
		$sUrl = $this->getVcsUrlForFileAndVersion( $sFileFragment, $sVersion, $bUseSiteLocale );
		try {
			( new Services\Utilities\HttpUtil() )->checkUrl( $sUrl );
			$bExists = true;
		}
		catch ( \Exception $oE ) {
			$bExists = false;
		}
		return $bExists;
	}

	/**
	 * @param string $sFileFragment
	 * @param string $sVersion
	 * @param bool   $bUseSiteLocale
	 * @return string
	 */
	abstract protected function getVcsUrlForFileAndVersion( $sFileFragment, $sVersion, $bUseSiteLocale = true );
}