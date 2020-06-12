<?php

namespace FernleafSystems\Wordpress\Services\Core;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class Rest
 * @package FernleafSystems\Wordpress\Services\Core
 */
class Rest {

	/**
	 * @return string|null
	 */
	public function getNamespace() {
		$sNameSpace = null;

		$sRoute = $this->getRoute();
		if ( !empty( $sRoute ) ) {
			$aParts = array_filter( explode( '/', $sRoute ) );
			if ( !empty( $aParts ) ) {
				$sNameSpace = array_shift( $aParts );
			}
		}
		return $sNameSpace;
	}

	/**
	 * @return string|null
	 */
	public function getRoute() {
		$sRoute = null;

		if ( $this->isRest() ) {
			$oReq = Services::Request();
			$oWp = Services::WpGeneral();

			$sRoute = $oReq->request( 'rest_route' );
			if ( empty( $sRoute ) && $oWp->isPermalinksEnabled() ) {
				$sFullUri = $oWp->getHomeUrl( $oReq->getPath() );
				$sRoute = substr( $sFullUri, strlen( get_rest_url( get_current_blog_id() ) ) );
			}
		}
		return $sRoute;
	}

	/**
	 * @return bool
	 */
	public function isRest() {
		$bIsRest = ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || !empty( $_REQUEST[ 'rest_route' ] );

		global $wp_rewrite;
		if ( !$bIsRest && function_exists( 'rest_url' ) && is_object( $wp_rewrite ) ) {
			$sRestUrlBase = get_rest_url( get_current_blog_id(), '/' );
			$sRestPath = trim( parse_url( $sRestUrlBase, PHP_URL_PATH ), '/' );
			$sRequestPath = trim( Services::Request()->getPath(), '/' );
			$bIsRest = !empty( $sRequestPath ) && !empty( $sRestPath )
					   && ( strpos( $sRequestPath, $sRestPath ) === 0 );
		}
		return $bIsRest;
	}

	/**
	 * @return string|null
	 * @deprecated
	 */
	protected function getPath() {
		return $this->getRoute();
	}
}