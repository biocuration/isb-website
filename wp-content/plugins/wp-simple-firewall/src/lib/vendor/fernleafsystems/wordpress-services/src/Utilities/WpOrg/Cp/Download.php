<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Cp;

use FernleafSystems\Wordpress\Services\Services;
use FernleafSystems\Wordpress\Services\Utilities\HttpUtil;

class Download {

	/**
	 * @param string $sVersion
	 * @return string
	 * @throws \Exception
	 */
	public function version( $sVersion ) {
		$sTmpFile = null;

		try {
			$sUrl = $this->getZipDownloadUrl( $sVersion );
			if ( !empty( $sVersion ) ) {
				$sTmpFile = ( new HttpUtil() )
					->downloadUrl( $sUrl );
			}
		}
		catch ( \Exception $oE ) {
		}
		return $sTmpFile;
	}

	/**
	 * @param $sVersion
	 * @return string|null
	 */
	private function getZipDownloadUrl( $sVersion ) {
		$sUrl = null;
		$aVersions = @json_decode( Services::HttpRequest()->getContent( Repo::GetUrlForVersions() ), true );

		if ( is_array( $aVersions ) ) {
			foreach ( $aVersions as $aVers ) {
				if ( $aVers[ 'tag_name' ] == $sVersion ) {
					$sUrl = $aVers[ 'zipball_url' ];
					break;
				}
			}
		}

		return $sUrl;
	}
}