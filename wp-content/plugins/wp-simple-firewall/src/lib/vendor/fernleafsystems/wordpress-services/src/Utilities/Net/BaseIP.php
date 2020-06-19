<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Net;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class BaseIP
 * @package FernleafSystems\Wordpress\Services\Utilities\Net
 */
class BaseIP {

	/**
	 * @param string $sSource
	 * @return string[]
	 */
	public function getIpsFromSource( $sSource ) {
		return array_filter(
			array_map( 'trim', explode( ',', (string)Services::Request()->server( $sSource ) ) ),
			function ( $sIp ) {
				if ( substr_count( $sIp, ':' ) === 1 ) { // "IP:PORT"
					$sIp = substr( $sIp, 0, strpos( $sIp, ':' ) );
				}
				return filter_var( $sIp, FILTER_VALIDATE_IP ) !== false;
			}
		);
	}

	/**
	 * @return string[]
	 */
	public function getIpSourceOptions() {
		return [
			'REMOTE_ADDR',
			'HTTP_CF_CONNECTING_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_REAL_IP',
			'HTTP_X_SUCURI_CLIENTIP',
			'HTTP_INCAP_CLIENT_IP',
			'HTTP_X_SP_FORWARDED_IP',
			'HTTP_FORWARDED',
			'HTTP_CLIENT_IP'
		];
	}
}