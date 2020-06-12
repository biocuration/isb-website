<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin;

use FernleafSystems\Wordpress\Services\Utilities\HttpUtil;

/**
 * Class Download
 * @package FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin
 */
class Download {

	use Base;

	/**
	 * @param string $sVersion
	 * @return string|null
	 */
	public function getDownloadUrlForVersion( $sVersion ) {
		$aAll = ( new Versions() )
			->setWorkingSlug( $this->getWorkingSlug() )
			->allVersionsUrls();
		return empty( $aAll[ $sVersion ] ) ? null : $aAll[ $sVersion ];
	}

	/**
	 * @return string|null
	 * @throws \Exception
	 */
	public function latest() {
		$sUrl = ( new Versions() )
			->setWorkingSlug( $this->getWorkingSlug() )
			->latest();
		return empty( $sUrl ) ? null : ( new HttpUtil() )->downloadUrl( $sUrl );
	}

	/**
	 * @param string $sVersion
	 * @return string
	 * @throws \Exception
	 */
	public function version( $sVersion ) {
		$sUrl = $this->getDownloadUrlForVersion( $sVersion );
		return empty( $sUrl ) ? null : ( new HttpUtil() )->downloadUrl( $sUrl );
	}
}