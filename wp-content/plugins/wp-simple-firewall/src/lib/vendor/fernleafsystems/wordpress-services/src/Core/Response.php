<?php

namespace FernleafSystems\Wordpress\Services\Core;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class Response
 * @package FernleafSystems\Wordpress\Services\Core
 */
class Response {

	/**
	 * Response constructor.
	 */
	public function __construct() {
	}

	/**
	 * @param string $sKey
	 * @return bool
	 */
	public function cookieDelete( $sKey ) {
		unset( $_COOKIE[ $sKey ] );
		return $this->cookieSet( $sKey, '', -1000000 );
	}

	/**
	 * @param string $sKey
	 * @param string $mValue
	 * @param int    $nExpireLength
	 * @param null   $sPath
	 * @param null   $sDomain
	 * @param bool   $bSsl
	 * @return bool
	 */
	public function cookieSet( $sKey, $mValue, $nExpireLength = 3600, $sPath = null, $sDomain = null, $bSsl = null ) {
		if ( function_exists( 'headers_sent' ) && headers_sent() ) {
			return false;
		}
		$_COOKIE[ $sKey ] = $mValue;
		return setcookie(
			$sKey,
			$mValue,
			(int)( Services::Request()->ts() + $nExpireLength ),
			( is_null( $sPath ) && defined( 'COOKIEPATH' ) ) ? COOKIEPATH : $sPath,
			( is_null( $sDomain ) && defined( 'COOKIE_DOMAIN' ) ) ? COOKIE_DOMAIN : $sDomain,
			is_null( $bSsl ) ? ( is_ssl() ? true : false ) : $bSsl
		);
	}

	/**
	 * @param string $sStringContent
	 * @param string $sFilename
	 * @return bool
	 */
	public function downloadStringAsFile( $sStringContent, $sFilename ) {
		header( "Content-type: application/octet-stream" );
		header( "Content-disposition: attachment; filename=".$sFilename );
		header( "Content-Transfer-Encoding: binary" );
		header( "Content-Length: ".strlen( $sStringContent ) );
		echo $sStringContent;
		die();
	}

	/**
	 * @param string $sUrl
	 * @param array  $aQueryParams
	 * @param bool   $bSafe
	 * @param bool   $bProtectAgainstInfiniteLoops - if false, ignores the redirect loop protection
	 */
	public function redirect( $sUrl, $aQueryParams = [], $bSafe = true, $bProtectAgainstInfiniteLoops = true ) {
		$sUrl = empty( $aQueryParams ) ? $sUrl : add_query_arg( $aQueryParams, $sUrl );

		// we prevent any repetitive redirect loops
		if ( $bProtectAgainstInfiniteLoops ) {
			if ( Services::Request()->cookie( 'icwp-isredirect' ) == 'yes' ) {
				return;
			}
			else {
				Services::Data()->setCookie( 'icwp-isredirect', 'yes', 7 );
			}
		}

		// based on: https://make.wordpress.org/plugins/2015/04/20/fixing-add_query_arg-and-remove_query_arg-usage/
		// we now escape the URL to be absolutely sure since we can't guarantee the URL coming through there
		$sUrl = esc_url_raw( $sUrl );
		header( 'Cache-Control: no-store, no-cache' );
		$bSafe ? wp_safe_redirect( $sUrl ) : wp_redirect( $sUrl );
		exit();
	}

	/**
	 * @param array $aQueryParams
	 */
	public function redirectHere( $aQueryParams = [] ) {
		$this->redirect( Services::Request()->getUri(), $aQueryParams );
	}

	/**
	 * @param array $aQueryParams
	 */
	public function redirectToLogin( $aQueryParams = [] ) {
		$this->redirect( wp_login_url(), $aQueryParams );
	}

	/**
	 * @param array $aQueryParams
	 */
	public function redirectToAdmin( $aQueryParams = [] ) {
		$this->redirect( is_multisite() ? get_admin_url() : admin_url(), $aQueryParams );
	}

	/**
	 * @param array $aQueryParams
	 */
	public function redirectToHome( $aQueryParams = [] ) {
		$this->redirect( home_url(), $aQueryParams );
	}

	/**
	 * @param string $sRequestedUriPath
	 * @param string $sHostName - you can also send a full and valid URL
	 */
	public function sendApache404( $sRequestedUriPath = '', $sHostName = '' ) {
		$oReq = Services::Request();
		if ( empty( $sRequestedUriPath ) ) {
			$sRequestedUriPath = $oReq->getUri();
		}

		if ( empty( $sHostName ) ) {
			$sHostName = $oReq->server( 'SERVER_NAME' );
		}
		elseif ( filter_var( $sHostName, FILTER_VALIDATE_URL ) ) {
			$sHostName = parse_url( $sRequestedUriPath, PHP_URL_HOST );
		}

		$bSsl = is_ssl() || $oReq->server( 'HTTP_X_FORWARDED_PROTO' ) == 'https';
		header( 'HTTP/1.1 404 Not Found' );

		$nPort = $bSsl ? 443 : (int)$oReq->server( 'SERVER_PORT' );
		$sDie = sprintf(
			'<html><head><title>404 Not Found</title><style type="text/css"></style></head><body><h1>Not Found</h1><p>The requested URL %s was not found on this server.</p><p>Additionally, a 404 Not Found error was encountered while trying to use an ErrorDocument to handle the request.</p><hr><address>Apache Server at %s Port %s</address></body></html>',
			preg_replace( '#[^a-z0-9_&;=%/-]#i', '', esc_html( $sRequestedUriPath ) ),
			$sHostName,
			empty( $nPort ) ? 80 : $nPort
		);
		die( $sDie );
	}
}