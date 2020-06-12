<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Licenses;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class EddLicenseVO
 * @package FernleafSystems\Wordpress\Services\Utilities\Licenses
 * @property int    $activations_left
 * @property string $customer_email
 * @property string $checksum
 * @property string $customer_name
 * @property string $item_name
 * @property string $expires    - date string or "lifetime"
 * @property int    $expires_at - unix timestamp
 * @property int    $last_request_at
 * @property int    $last_verified_at
 * @property int    $license_limit
 * @property int    $site_count
 * @property string $license
 * @property string $payment_id
 * @property bool   $success
 * @property string $error
 */
class EddLicenseVO {

	use \FernleafSystems\Utilities\Data\Adapter\StdClassAdapter;

	/**
	 * @return int
	 */
	public function getExpiresAt() {
		return ( $this->expires == 'lifetime' ) ? PHP_INT_MAX : strtotime( $this->expires );
	}

	/**
	 * @return bool
	 */
	public function isExpired() {
		return ( $this->getExpiresAt() < Services::Request()->ts() );
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return ( $this->isReady() && $this->success && !$this->isExpired() && $this->license == 'valid' );
	}

	/**
	 * @return bool
	 */
	public function hasError() {
		return !empty( $this->error );
	}

	/**
	 * @return bool
	 */
	public function hasChecksum() {
		return !empty( $this->checksum );
	}

	/**
	 * @return bool
	 */
	public function isReady() {
		return $this->hasChecksum();
	}

	/**
	 * @param bool $bAddRandom
	 * @return $this
	 */
	public function updateLastVerifiedAt( $bAddRandom = false ) {
		$this->last_verified_at = (int)$this->last_request_at +
								  ( $bAddRandom ? rand( -6, 18 )*HOUR_IN_SECONDS : 0 );
		return $this;
	}
}