<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Encrypt;

use FernleafSystems\Utilities\Data\Adapter\StdClassAdapter;

/**
 * Class EncryptVo
 * @package FernleafSystems\Wordpress\Services\Utilities\Encrypt
 * @property bool   $success
 * @property int    $result
 * @property string $message
 * @property bool   $json_encoded
 * @property string $sealed_data
 * @property string $sealed_password
 */
class OpenSslEncryptVo {

	use StdClassAdapter {
		__get as __adapterGet;
		__set as __adapterSet;
	}

	/**
	 * @param string $sProperty
	 * @return mixed
	 */
	public function __get( $sProperty ) {

		$mVal = $this->__adapterGet( $sProperty );

		switch ( $sProperty ) {

			case 'sealed_data':
			case 'sealed_password':
				$mVal = base64_decode( $mVal );
				break;

			default:
				break;
		}

		return $mVal;
	}

	/**
	 * @param string $sProperty
	 * @param mixed  $mValue
	 * @return $this
	 */
	public function __set( $sProperty, $mValue ) {

		switch ( $sProperty ) {

			case 'sealed_data':
			case 'sealed_password':
				$mValue = base64_encode( $mValue );
				break;

			default:
				break;
		}

		return $this->__adapterSet( $sProperty, $mValue );
	}
}