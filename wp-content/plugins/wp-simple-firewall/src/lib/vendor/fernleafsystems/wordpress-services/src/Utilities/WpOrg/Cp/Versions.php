<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Cp;

use FernleafSystems\Wordpress\Services;

/**
 * Class Versions
 * @package FernleafSystems\Wordpress\Services\Utilities\WpOrg\Cp
 */
class Versions extends Services\Utilities\WpOrg\Base\VersionsBase {

	/**
	 * @param string $sVersion
	 * @param bool   $bVerifyUrl
	 * @return bool
	 */
	public function exists( $sVersion, $bVerifyUrl = false ) {
		$bExists = in_array( $sVersion, $this->all() );
		if ( $bExists && $bVerifyUrl ) {
			try {
				( new Services\Utilities\HttpUtil() )->checkUrl( Repo::GetUrlForVersion( $sVersion ) );
			}
			catch ( \Exception $oE ) {
				$bExists = false;
			}
		}
		return $bExists;
	}

	/**
	 * @return array
	 */
	protected function downloadVersions() {
		$aV = [];
		$aVersions = @json_decode( Services\Services::HttpRequest()
													->getContent( Repo::GetUrlForVersions() ), true );
		if ( is_array( $aVersions ) ) {
			$aV = array_map(
				function ( $aVersData ) {
					return $aVersData[ 'tag_name' ];
				},
				$aVersions
			);
		}

		return $aV;
	}
}