<?php

namespace FernleafSystems\Wordpress\Services\Core;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class Includes
 * @package FernleafSystems\Wordpress\Services\Core
 */
class Includes {

	/**
	 * @return string
	 */
	public function getUrl_Jquery() {
		return $this->getJsUrl( 'jquery/jquery.js' );
	}

	/**
	 * @param string $sJsInclude
	 * @return string
	 */
	public function getJsUrl( $sJsInclude ) {
		return $this->getIncludeUrl( path_join( 'js', $sJsInclude ) );
	}

	/**
	 * @param string $sInclude
	 * @return string
	 */
	public function getIncludeUrl( $sInclude ) {
		$sInclude = path_join( 'wp-includes', $sInclude );
		return $this->addIncludeModifiedParam( path_join( Services::WpGeneral()->getWpUrl(), $sInclude ), $sInclude );
	}

	/**
	 * @param string $sIncludeHandle
	 * @param string $sAttribute
	 * @param string $sValue
	 * @return $this
	 */
	public function addIncludeAttribute( $sIncludeHandle, $sAttribute, $sValue ) {
		add_filter( 'script_loader_tag',
			function ( $sTag, $sHandle ) use ( $sIncludeHandle, $sAttribute, $sValue ) {
				if ( $sHandle == $sIncludeHandle && strpos( $sTag, $sAttribute.'=' ) === false ) {
					$sTag = str_replace( ' src', sprintf( ' %s="%s" src', $sAttribute, $sValue ), $sTag );
				}
				return $sTag;
			},
			10, 2
		);
		return $this;
	}

	/**
	 * @param $sUrl
	 * @param $sInclude
	 * @return string
	 */
	public function addIncludeModifiedParam( $sUrl, $sInclude ) {
		$nTime = Services::WpFs()->getModifiedTime( path_join( ABSPATH, $sInclude ) );
		return add_query_arg( [ 'mtime' => $nTime ], $sUrl );
	}
}