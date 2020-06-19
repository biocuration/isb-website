<?php

namespace FernleafSystems\Wordpress\Services\Utilities\File;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Useful so we know which new line character is used to split up the lines: "\n"
 * This is preferable to just using file()
 *
 * Class GetFileAsArray
 * @package FernleafSystems\Wordpress\Services\Utilities\File
 */
class GetFileAsArray {

	/**
	 * @param string $sPath
	 * @param string $sExplodeOn
	 * @return string[]
	 * @throws \Exception
	 */
	public function run( $sPath, $sExplodeOn = "\n" ) {
		$oFs = Services::WpFs();
		if ( !$oFs->isFile( $sPath ) ) {
			throw new \InvalidArgumentException( 'File does not exist' );
		}

		$sContents = $oFs->getFileContent( $sPath );
		if ( empty( $sContents ) ) {
			throw new \Exception( 'File is empty' );
		}

		return explode( $sExplodeOn, $sContents );
	}
}