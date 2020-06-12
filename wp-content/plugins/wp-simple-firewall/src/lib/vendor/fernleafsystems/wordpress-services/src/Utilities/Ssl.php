<?php

namespace FernleafSystems\Wordpress\Services\Utilities;

/**
 * Class Ssl
 * @package FernleafSystems\Wordpress\Services\Utilities
 */
class Ssl {

	/**
	 * @var array
	 */
	private static $aCache;

	public function __construct() {
		self::$aCache = [];
	}

	/**
	 * @return bool
	 */
	public function isEnvSupported() {
		$aFunctions = [
			'stream_context_create',
			'stream_socket_client',
			'stream_context_get_params',
			'openssl_x509_parse',
		];

		$bFunctionsAvailable = true;
		foreach ( $aFunctions as $sFunction ) {
			$bFunctionsAvailable = $bFunctionsAvailable && function_exists( $sFunction )
								   && is_callable( $sFunction );
		}

		return $bFunctionsAvailable;
	}

	/**
	 * @param string $sHost
	 * @return array
	 * @throws \Exception
	 */
	public function getCertDetailsForDomain( $sHost ) {
		if ( !$this->isEnvSupported() ) {
			throw new \Exception( 'The environment does not support this' );
		}

		if ( filter_var( $sHost, FILTER_VALIDATE_URL ) ) {
			$sHost = parse_url( $sHost, PHP_URL_HOST );
		}

		if ( empty( self::$aCache[ $sHost ] ) ) {

			$oContext = stream_context_create(
				[
					'ssl' => [
						'capture_peer_cert' => true,
						'verify_peer'       => true,
						'verify_peer_name'  => true,
					]
				]
			);

			$rSocketClient = @stream_socket_client(
				sprintf( 'ssl://%s:443', $sHost ),
				$errno, $errstr, 3,
				STREAM_CLIENT_CONNECT,
				$oContext
			);

			if ( !is_resource( $rSocketClient ) ) {
				throw new \Exception( 'Stream Socket client failed to retrieve SSL Cert resource.' );
			}

			$aResponseParams = stream_context_get_params( $rSocketClient );
			if ( empty( $aResponseParams[ 'options' ][ 'ssl' ][ 'peer_certificate' ] ) ) {
				throw new \Exception( 'Peer Certificate field was empty in the response.' );
			}
			self::$aCache[ $sHost ] = openssl_x509_parse( $aResponseParams[ 'options' ][ 'ssl' ][ 'peer_certificate' ] );
		}

		if ( empty( self::$aCache[ $sHost ] ) ) {
			throw new \Exception( 'Parsing certificate failed.' );
		}

		return self::$aCache[ $sHost ];
	}

	/**
	 * @param string $sHost
	 * @return int
	 */
	public function getExpiresAt( $sHost ) {
		$nExpiresAt = 0;
		try {
			$aCert = $this->getCertDetailsForDomain( $sHost );
			if ( !empty( $aCert[ 'validTo_time_t' ] ) ) {
				$nExpiresAt = $aCert[ 'validTo_time_t' ];
			}
		}
		catch ( \Exception $oE ) {
		}
		return $nExpiresAt;
	}
}