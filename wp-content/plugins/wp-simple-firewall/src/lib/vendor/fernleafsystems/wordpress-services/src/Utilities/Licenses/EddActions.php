<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Licenses;

class EddActions {

	/**
	 * @param string $sUrl
	 * @return string
	 */
	public static function CleanUrl( $sUrl ) {
		$sUrl = preg_replace( '#^(https?:/{1,2})?(www\.)?#', '', mb_strtolower( trim( $sUrl ) ) );
		if ( strpos( $sUrl, '?' ) ) {
			$sUrl = explode( '?', $sUrl, 2 )[ 0 ];
		}
		return sanitize_text_field( trailingslashit( $sUrl ) );
	}
}