<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Vulnerabilities;

use FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes;

abstract class Base extends WpHashes\ApiBase {

	const API_ENDPOINT = 'vulnerabilities';
	const ASSET_TYPE = '';
	const RESPONSE_DATA_KEY = 'vulnerabilities';

	/**
	 * @return array[]|null
	 */
	public function query() {
		return parent::query();
	}

	/**
	 * @return string
	 */
	protected function getApiUrl() {
		return parent::getApiUrl().'/'.$this->getRequestVO()->type;
	}

	/**
	 * @return RequestVO
	 */
	protected function getRequestVO() {
		$oReq = parent::getRequestVO();
		$oReq->type = static::ASSET_TYPE;
		return $oReq;
	}

	/**
	 * @return RequestVO
	 */
	protected function newReqVO() {
		return new RequestVO();
	}
}