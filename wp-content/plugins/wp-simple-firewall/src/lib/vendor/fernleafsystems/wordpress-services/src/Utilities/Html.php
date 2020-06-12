<?php

namespace FernleafSystems\Wordpress\Services\Utilities;

/**
 * Class Html
 * @package FernleafSystems\Wordpress\Services\Utilities
 */
class Html {

	/**
	 * @param string $sHref
	 * @param string $sTxt
	 * @param string $sTarget
	 * @param array  $aArgs
	 * @return string
	 */
	public function href( $sHref, $sTxt, $sTarget = '_blank', $aArgs = [] ) {
		foreach ( $aArgs as $sKey => $sVal ) {
			$aArgs[ $sKey ] = sprintf( '%s="%s"', $sKey, $sVal );
		}
		return sprintf(
			'<a href="%s" target="%s"%s>%s</a>',
			$sHref, $sTarget, ( empty( $aArgs ) ? '' : ' '.implode( ' ', $aArgs ) ), $sTxt
		);
	}
}