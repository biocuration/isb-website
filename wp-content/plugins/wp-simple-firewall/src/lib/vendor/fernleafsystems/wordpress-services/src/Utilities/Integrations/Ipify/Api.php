<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Integrations\Ipify;

use FernleafSystems\Wordpress\Services\Services;

class Api {

	const IpifyEndpoint4 = 'https://api.ipify.org';
	const IpifyEndpoint6 = 'https://api6.ipify.org';

	/**
	 * @return string[]
	 */
	public function getMyIps() {
		return array_unique( array_filter( [
			$this->getMyIp4(),
			$this->getMyIp6(),
			Services::Request()->getServerAddress()
		] ) );
	}

	/**
	 * @return string
	 */
	public function getMyIp4() {
		return $this->sendReq( static::IpifyEndpoint4 );
	}

	/**
	 * @return string
	 */
	public function getMyIp6() {
		return $this->sendReq( static::IpifyEndpoint6 );
	}

	/**
	 * @param string $sEndpoint
	 * @return string
	 */
	protected function sendReq( $sEndpoint ) {
		return trim( Services::HttpRequest()->getContent( $sEndpoint ) );
	}
}