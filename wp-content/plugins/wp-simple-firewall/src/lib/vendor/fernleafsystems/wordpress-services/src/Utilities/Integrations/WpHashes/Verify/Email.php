<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Verify;

/**
 * Class Email
 * @package FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Verify
 */
class Email extends Base {

	/**
	 * @param string $sEmail
	 * @return array|null
	 */
	public function getEmailVerification( $sEmail ) {
		$oReq = $this->getRequestVO();
		$oReq->action = 'email';
		$oReq->address = $sEmail;
		return $this->query();
	}

	/**
	 * @return string
	 */
	protected function getApiUrl() {
		$aData = array_map( 'rawurlencode', array_filter( array_merge(
			[
				'action'  => false,
				'address' => false,
			],
			$this->getRequestVO()->getRawDataAsArray()
		) ) );
		return sprintf( '%s/%s', parent::getApiUrl(), implode( '/', $aData ) );
	}
}