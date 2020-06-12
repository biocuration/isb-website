<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Licenses\Keyless;

/**
 * Class Ping
 * @package FernleafSystems\Wordpress\Services\Utilities\Licenses\Keyless
 */
class Ping extends Base {

	const API_ACTION = 'ping';

	/**
	 * @return bool
	 */
	public function ping() {
		$sPong = '';

		$aRaw = $this->sendReq();
		if ( is_array( $aRaw ) && !empty( $aRaw[ 'keyless' ] ) && !empty( $aRaw[ 'keyless' ][ self::API_ACTION ] ) ) {
			$sPong = $aRaw[ 'keyless' ][ self::API_ACTION ];
		}

		return $sPong === 'pong';
	}
}