<?php

namespace FernleafSystems\Wordpress\Services\Utilities\File;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class WriteDataToFileEncrypted
 * @package FernleafSystems\Wordpress\Services\Utilities\File
 */
class WriteDataToFileEncrypted {

	/**
	 * @param string $sPath
	 * @param string $sData
	 * @param string $sPublicKey
	 * @param string $sPrivateKeyForVerify - verify writing successful if private key supplied
	 * @return bool
	 * @throws \Exception
	 */
	public function run( $sPath, $sData, $sPublicKey, $sPrivateKeyForVerify = null ) {
		$oEncrypt = Services::Encrypt();

		$oEncrypted = $oEncrypt->sealData( $sData, $sPublicKey );
		if ( !$oEncrypted->success ) {
			throw new \Exception( 'Could not seal data with message: '.$oEncrypted->message );
		}

		$bSuccess = Services::WpFs()->putFileContent( $sPath, json_encode( $oEncrypted->getRawDataAsArray() ) );
		if ( $bSuccess && !empty( $sPrivateKeyForVerify ) ) {
			$bSuccess = ( new ReadDataFromFileEncrypted() )->run( $sPath, $sPrivateKeyForVerify ) === $sData;
		}
		return $bSuccess;
	}
}