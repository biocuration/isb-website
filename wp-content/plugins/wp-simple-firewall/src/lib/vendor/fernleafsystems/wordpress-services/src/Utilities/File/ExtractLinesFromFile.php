<?php

namespace FernleafSystems\Wordpress\Services\Utilities\File;

/**
 * Class ExtractLineFromFile
 * @package FernleafSystems\Wordpress\Services\Utilities\File
 */
class ExtractLinesFromFile {

	/**
	 * @param string $sPath
	 * @param int[]  $aLines
	 * @return string[]
	 * @throws \Exception
	 */
	public function run( $sPath, $aLines ) {
		$aLines = array_intersect_key(
			( new GetFileAsArray() )->run( $sPath ),
			array_flip( $aLines )
		);
		return $aLines;
	}
}