<?php

namespace FernleafSystems\Wordpress\Services\Utilities;

class Obfuscate {

	/**
	 * @param string $sEmail
	 * @return string
	 */
	public static function Email( $sEmail ) {
		list( $sP1, $sP2 ) = explode( '@', $sEmail, 2 );
		return substr( $sP1, 0, 1 ).'****'.substr( $sP1, -1, 1 )
			   .'@'.
			   substr( $sP2, 0, 1 ).'****'.substr( $sP2, strrpos( $sP2, '.' ) );
	}
}