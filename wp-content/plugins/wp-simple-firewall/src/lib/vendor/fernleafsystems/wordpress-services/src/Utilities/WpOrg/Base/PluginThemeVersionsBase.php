<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Base;

use FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin;
use FernleafSystems\Wordpress\Services\Utilities\WpOrg\Theme;

abstract class PluginThemeVersionsBase {

	/**
	 * @return string[]
	 */
	public function all() {
		$aVersions = array_filter( array_keys( $this->allVersionsUrls() ) );
		usort( $aVersions, 'version_compare' );
		return $aVersions;
	}

	/**
	 * @return string[]
	 */
	public function allVersionsUrls() {
		$aVersions = [];
		$sSlug = $this->getWorkingSlug();
		if ( !empty( $sSlug ) ) {
			try {
				$oInfo = $this->getApi()
							  ->setWorkingSlug( $sSlug )
							  ->getInfo();
				$aVersions = isset( $oInfo->versions ) ? $oInfo->versions : [];
			}
			catch ( \Exception $oE ) {
			}
		}
		return is_array( $aVersions ) ? $aVersions : [];
	}

	/**
	 * @return Plugin\Api|Theme\Api
	 */
	abstract protected function getApi();

	/**
	 * @return string
	 */
	abstract protected function getWorkingSlug();

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function latest() {
		return $this->getApi()
					->setWorkingSlug( $this->getWorkingSlug() )
					->getInfo()->version;
	}

	/**
	 * @param string $sVersion
	 * @param bool   $bVerifyUrl
	 * @return bool
	 */
	abstract public function exists( $sVersion, $bVerifyUrl = false );
}