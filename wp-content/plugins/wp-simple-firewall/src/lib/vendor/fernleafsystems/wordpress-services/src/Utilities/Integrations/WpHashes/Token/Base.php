<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Token;

use FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes;

abstract class Base extends WpHashes\ApiBase {

	const API_ENDPOINT = 'token';
	const RESPONSE_DATA_KEY = 'token';

	/**
	 * @return RequestVO
	 */
	protected function newReqVO() {
		return new RequestVO();
	}
}