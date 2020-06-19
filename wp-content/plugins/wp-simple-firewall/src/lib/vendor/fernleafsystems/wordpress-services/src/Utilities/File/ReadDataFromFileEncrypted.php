<?php

namespace FernleafSystems\Wordpress\Services\Utilities\File;

use FernleafSystems\Wordpress\Services\Services;
use FernleafSystems\Wordpress\Services\Utilities\Encrypt\OpenSslEncryptVo;

/**
 * Class ReadDataFromFileEncrypted
 * @package FernleafSystems\Wordpress\Services\Utilities\File
 */
class ReadDataFromFileEncrypted {

	/**
	 * @param string $sPath
	 * @param string $sPrivateKey
	 * @return string
	 * @throws \Exception
	 */
	public function run( $sPath, $sPrivateKey ) {
		$oFs = Services::WpFs();
		if ( !$oFs->exists( $sPath ) || !$oFs->isFile( $sPath ) ) {
			throw new \Exception( 'File path does not exist: '.$sPath );
		}
		$sRawFile = $oFs->getFileContent( $sPath );
		if ( empty( $sRawFile ) ) {
			throw new \Exception( 'Could not read data from file: '.$sRawFile );
		}
		$aRawData = @json_decode( $sRawFile, true );
		if ( empty( $aRawData ) || !is_array( $aRawData ) ) {
			throw new \Exception( 'Parsing raw data from file failed' );
		}

		$oVo = ( new OpenSslEncryptVo() )->applyFromArray( $aRawData );

		$sData = Services::Encrypt()->openData( $oVo->sealed_data, $oVo->sealed_password, $sPrivateKey );
		if ( $sData === false ) {
			throw new \Exception( 'Decrypting sealed data failed.' );
		}
		return $sData;
	}
}