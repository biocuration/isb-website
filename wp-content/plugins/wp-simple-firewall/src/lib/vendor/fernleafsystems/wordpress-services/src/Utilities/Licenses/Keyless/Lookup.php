<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Licenses\Keyless;

use FernleafSystems\Wordpress\Services\Services;
use FernleafSystems\Wordpress\Services\Utilities\Licenses\EddLicenseVO;

/**
 * Class Lookup
 * @package FernleafSystems\Wordpress\Services\Utilities\Licenses\Keyless
 * @property int    $item_id
 * @property string $install_id
 * @property string $url
 * @property string $nonce
 * @property array  $meta
 */
class Lookup extends Base {

	const API_ACTION = 'lookup';

	/**
	 * @return EddLicenseVO
	 */
	public function lookup() {
		if ( empty( $this->url ) ) {
			$this->url = Services::WpGeneral()->getHomeUrl( '', true );
		}

		$aRaw = $this->sendReq();
		if ( is_array( $aRaw ) && !empty( $aRaw[ 'keyless' ] ) && !empty( $aRaw[ 'keyless' ][ 'license' ] ) ) {
			$aLicenseInfo = $aRaw[ 'keyless' ][ 'license' ];
		}
		else {
			$aLicenseInfo = [];
		}

		$oLic = ( new EddLicenseVO() )->applyFromArray( $aLicenseInfo );
		$oLic->last_request_at = Services::Request()->ts();
		return $oLic;
	}

	/**
	 * @return string
	 */
	protected function getApiRequestUrl() {
		return sprintf( '%s/%s/%s', parent::getApiRequestUrl(), $this->item_id, $this->install_id );
	}

	/**
	 * @return string[]
	 */
	protected function getRequestBodyParamKeys() {
		return [
			'url',
			'nonce',
			'meta',
		];
	}
}