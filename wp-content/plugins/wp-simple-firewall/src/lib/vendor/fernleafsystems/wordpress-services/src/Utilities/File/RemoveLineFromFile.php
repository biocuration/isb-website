<?php

namespace FernleafSystems\Wordpress\Services\Utilities\File;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class RemoveLineFromFile
 * @package FernleafSystems\Wordpress\Services\Utilities\File
 */
class RemoveLineFromFile {

	/**
	 * @param string $sPath
	 * @param int    $nLine
	 * @return string
	 * @throws \Exception
	 */
	public function run( $sPath, $nLine ) {

		$aLines = ( new GetFileAsArray() )->run( $sPath );
		if ( !array_key_exists( $nLine, $aLines ) ) {
			throw new \Exception( 'Line does not exist.' );
		}

		$sLine = $aLines[ $nLine ];
		unset( $aLines[ $nLine ] );
		if ( !Services::WpFs()->putFileContent( $sPath, implode( "\n", $aLines ) ) ) {
			throw new \Exception( 'Could not write adjusted file.' );
		}

		return $sLine;
	}
}