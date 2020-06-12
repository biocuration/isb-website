<?php

namespace FernleafSystems\Wordpress\Services\Utilities;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class HttpUtil
 * @package FernleafSystems\Wordpress\Services\Utilities
 */
class HttpUtil {

	/**
	 * @var string[]
	 */
	private $aDownloads;

	public function __construct() {
		$this->aDownloads = [];
		add_action( 'shutdown', [ $this, 'deleteDownloads' ] );
	}

	public function deleteDownloads() {
		$oFS = Services::WpFs();
		foreach ( $this->aDownloads as $sFile ) {
			if ( $oFS->exists( $sFile ) ) {
				$oFS->deleteFile( $sFile );
			}
		}
	}

	/**
	 * @param string $sUrl
	 * @param array  $aValidResponseCodes
	 * @return $this
	 * @throws \Exception
	 */
	public function checkUrl( $sUrl, $aValidResponseCodes = [ 200, 304 ] ) {
		$oReq = new HttpRequest();
		if ( !$oReq->get( $sUrl ) ) {
			throw new \Exception( $oReq->lastError->get_error_message() );
		}

		if ( !in_array( $oReq->lastResponse->getCode(), $aValidResponseCodes ) ) {
			throw new \Exception( 'Head Request Failed. Likely the version does not exist.' );
		}

		return $this;
	}

	/**
	 * @param string $sUrl
	 * @return string
	 * @throws \Exception
	 */
	public function downloadUrl( $sUrl ) {
		/** @var string|\WP_Error $sFile */
		$sFile = download_url( $sUrl );
		if ( is_wp_error( $sFile ) ) {
			throw new \Exception( $sFile->get_error_message() );
		}
		if ( !realpath( $sFile ) ) {
			throw new \Exception( 'Downloaded could not be found' );
		}
		$this->aDownloads[] = $sFile;
		return $sFile;
	}
}