<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Encrypt;

/**
 * Class OpenSslEncrypt
 * @package FernleafSystems\Wordpress\Services\Utilities\Encrypt
 */
class OpenSslEncrypt {

	/**
	 * @param array $aArgs
	 * @return array - keys are private & public as pem strings
	 * @throws \Exception
	 */
	public function createNewPrivatePublicKeyPair( $aArgs = [] ) {
		$rKey = openssl_pkey_new( $aArgs );
		if ( empty( $rKey ) || !is_resource( $rKey ) ) {
			throw new \Exception( 'Could not generate new private key' );
		}
		if ( !openssl_pkey_export( $rKey, $sPriv ) || empty( $sPriv ) ) {
			throw new \Exception( 'Could not export new private key' );
		}
		$aPub = openssl_pkey_get_details( $rKey );
		if ( empty( $aPub ) || empty( $aPub[ 'key' ] ) ) {
			throw new \Exception( 'Could not generate public key from private' );
		}
		return [
			'private' => $sPriv,
			'public'  => $aPub[ 'key' ],
		];
	}

	/**
	 * @param string $sKey
	 * @return string
	 * @throws \Exception
	 */
	public function getPublicKeyFromPrivateKey( $sKey ) {
		$rKey = openssl_pkey_get_private( $sKey );
		if ( empty( $rKey ) || !is_resource( $rKey ) ) {
			throw new \Exception( 'Could not build private key' );
		}
		$aPub = openssl_pkey_get_details( $rKey );
		if ( empty( $aPub ) || empty( $aPub[ 'key' ] ) ) {
			throw new \Exception( 'Could not generate public key from private' );
		}
		return $aPub[ 'key' ];
	}

	/**
	 * @param OpenSslEncryptVo $oVo
	 * @param string           $sPrivateKey
	 * @return bool
	 */
	public function openDataVo( $oVo, $sPrivateKey ) {
		return $this->openData( $oVo->sealed_data, $oVo->sealed_password, $sPrivateKey );
	}

	/**
	 * @param string $sSealedData
	 * @param string $sSealedPassword
	 * @param string $sPrivateKey
	 * @return string|false
	 */
	public function openData( $sSealedData, $sSealedPassword, $sPrivateKey ) {
		$bResult = openssl_open( $sSealedData, $sOpenedData, $sSealedPassword, $sPrivateKey );
		return $bResult ? $sOpenedData : false;
	}

	/**
	 * @param mixed  $mDataToEncrypt
	 * @param string $sPublicKey
	 * @return OpenSslEncryptVo
	 */
	public function sealData( $mDataToEncrypt, $sPublicKey ) {

		$oVo = $this->getStandardEncryptResponse();

		if ( empty( $mDataToEncrypt ) ) {
			$oVo->success = false;
			$oVo->message = 'Data to encrypt was empty';
			return $oVo;
		}
		elseif ( !$this->isSupportedOpenSslDataEncryption() ) {
			$oVo->success = false;
			$oVo->message = 'Does not support OpenSSL data encryption';
		}
		else {
			$oVo->success = true;
		}

		// If at this stage we're not 'success' we return it.
		if ( !$oVo->success ) {
			return $oVo;
		}

		if ( !is_string( $mDataToEncrypt ) ) {
			$mDataToEncrypt = json_encode( $mDataToEncrypt );
			$oVo->json_encoded = true;
		}
		else {
			$oVo->json_encoded = false;
		}

		$aPasswordKeys = [];
		$nResult = openssl_seal( $mDataToEncrypt, $sEncryptedData, $aPasswordKeys, [ $sPublicKey ] );

		$oVo->result = $nResult;
		$oVo->success = is_int( $nResult ) && $nResult > 0 && !is_null( $sEncryptedData );
		if ( $oVo->success ) {
			$oVo->sealed_data = $sEncryptedData;
			$oVo->sealed_password = $aPasswordKeys[ 0 ];
		}

		return $oVo;
	}

	/**
	 * @return bool
	 */
	public function isSupportedOpenSsl() {
		return extension_loaded( 'openssl' );
	}

	/**
	 * @return bool
	 */
	public function isSupportedOpenSslSign() {
		return function_exists( 'base64_decode' )
			   && extension_loaded( 'openssl' )
			   && function_exists( 'openssl_sign' )
			   && function_exists( 'openssl_verify' )
			   && defined( 'OPENSSL_ALGO_SHA1' );
	}

	/**
	 * @return bool
	 */
	public function isSupportedOpenSslDataEncryption() {
		$bSupported = $this->isSupportedOpenSsl();
		$aFunc = [
			'openssl_seal',
			'openssl_open',
			'openssl_pkey_new',
			'openssl_pkey_export',
			'openssl_pkey_get_details',
			'openssl_pkey_get_private'
		];
		foreach ( $aFunc as $sFunc ) {
			$bSupported = $bSupported && function_exists( $sFunc );
		}
		return $bSupported;
	}

	/**
	 * @param string $sVerificationCode
	 * @param string $sSignature
	 * @param string $sPublicKey
	 * @return int                    1: Success; 0: Failure; -1: Error; -2: Not supported
	 */
	public function verifySslSignature( $sVerificationCode, $sSignature, $sPublicKey ) {
		$nResult = -2;
		if ( $this->isSupportedOpenSslSign() ) {
			$nResult = openssl_verify( $sVerificationCode, $sSignature, $sPublicKey );
		}
		return $nResult;
	}

	/**
	 * @return OpenSslEncryptVo
	 */
	protected function getStandardEncryptResponse() {
		return new OpenSslEncryptVo();
	}
}