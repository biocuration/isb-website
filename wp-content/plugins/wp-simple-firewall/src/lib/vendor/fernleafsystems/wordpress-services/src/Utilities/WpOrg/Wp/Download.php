<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Wp;

use FernleafSystems\Wordpress\Services\Utilities\HttpUtil;

class Download {

	use Base;
	const URL_DOWNLOAD = 'https://%swordpress.org/wordpress-%s%s.zip';

	/**
	 * @param string $sVersion
	 * @param string $sLocale - defaults to en_US
	 * @return string
	 * @throws \Exception
	 */
	public function version( $sVersion, $sLocale = '' ) {
		$sTmpFile = null;

		$sLocale = strtolower( $sLocale );
		if ( $sLocale == 'en_us' ) {
			$sLocale = '';
		}

		$sLocale = str_replace( '-', '_', $sLocale );

		if ( strpos( $sLocale, '_' ) ) {
			list( $pt1, $pt2 ) = explode( '_', $sLocale );
			$sLocale = $pt1.'_'.strtoupper( $pt2 );
		}

		$sUrl = sprintf(
			static::URL_DOWNLOAD,
			( empty( $sLocale ) ? '' : $sLocale.'.' ),
			$sVersion,
			( empty( $sLocale ) ? '' : '-'.$sLocale )
		);

		try {
			$sTmpFile = ( new HttpUtil() )->downloadUrl( $sUrl );
		}
		catch ( \Exception $oE ) {
		}
		return $sTmpFile;
	}
}