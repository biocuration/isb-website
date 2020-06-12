<?php

namespace FernleafSystems\Wordpress\Services\Utilities\File;

/**
 * Class ExtractLineFromFile
 * @package FernleafSystems\Wordpress\Services\Utilities\File
 */
class ExtractLineFromFile {

	/**
	 * @param string $sPath
	 * @param int    $nLine
	 * @return string
	 * @throws \Exception
	 */
	public function run( $sPath, $nLine ) {

		$aLines = ( new ExtractLinesFromFile() )->run( $sPath, [ $nLine ] );
		if ( !isset( $aLines[ $nLine ] ) ) {
			throw new \Exception( 'Line does not exist.' );
		}

		return $aLines[ $nLine ];
	}
}