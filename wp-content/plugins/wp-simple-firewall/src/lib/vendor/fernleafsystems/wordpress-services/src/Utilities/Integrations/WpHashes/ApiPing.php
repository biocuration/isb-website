<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes;

/**
 * Class ApiPing
 * @package FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes
 */
class ApiPing extends ApiBase {

	const API_ENDPOINT = 'ping';

	/**
	 * @return bool
	 */
	public function ping() {
		$aR = $this->query();
		return ( is_array( $aR ) && isset( $aR[ 'pong' ] ) ) ? ( $aR[ 'pong' ] == 'ping' ) : false;
	}
}