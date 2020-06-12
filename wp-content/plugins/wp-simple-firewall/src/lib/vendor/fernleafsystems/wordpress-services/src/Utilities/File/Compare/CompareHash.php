<?php

namespace FernleafSystems\Wordpress\Services\Utilities\File\Compare;

use FernleafSystems\Wordpress\Services\Services;

class CompareHash {

	/**
	 * @param string $sPath
	 * @param string $sHashToCompare
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public function isEqualFileMd5( $sPath, $sHashToCompare ) {
		if ( !Services::WpFs()->isFile( $sPath ) ) {
			throw new \InvalidArgumentException( 'File does not exist on disk to compare' );
		}
		if ( !is_string( $sHashToCompare ) ) {
			throw new \InvalidArgumentException( 'Provided user hash was not a string' );
		}

		$oDataManip = Services::DataManipulation();
		return hash_equals( md5_file( $sPath ), $sHashToCompare )
			   || hash_equals( md5( $oDataManip->convertLineEndingsDosToLinux( $sPath ) ), $sHashToCompare )
			   || hash_equals( md5( $oDataManip->convertLineEndingsLinuxToDos( $sPath ) ), $sHashToCompare );
	}

	/**
	 * @param string $sPath
	 * @param string $sHashToCompare
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public function isEqualFileSha1( $sPath, $sHashToCompare ) {
		if ( !Services::WpFs()->isFile( $sPath ) ) {
			throw new \InvalidArgumentException( 'File does not exist on disk to compare' );
		}
		if ( !is_string( $sHashToCompare ) ) {
			throw new \InvalidArgumentException( 'Provided user hash was not a string' );
		}

		$oDataManip = Services::DataManipulation();
		return hash_equals( sha1_file( $sPath ), $sHashToCompare )
			   || hash_equals( sha1( $oDataManip->convertLineEndingsDosToLinux( $sPath ) ), $sHashToCompare )
			   || hash_equals( sha1( $oDataManip->convertLineEndingsLinuxToDos( $sPath ) ), $sHashToCompare );
	}

	/**
	 * @param string $sPath1
	 * @param string $sPath2
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public function isEqualFilesMd5( $sPath1, $sPath2 ) {

		if ( !Services::WpFs()->isFile( $sPath2 ) ) {
			throw new \InvalidArgumentException( 'File does not exist on disk to compare' );
		}

		$oDataManip = Services::DataManipulation();
		return
			$this->isEqualFileMd5(
				$sPath1,
				md5( $oDataManip->convertLineEndingsDosToLinux( $sPath2 ) )
			)
			|| $this->isEqualFileMd5(
				$sPath1,
				md5( $oDataManip->convertLineEndingsLinuxToDos( $sPath2 ) )
			);
	}

	/**
	 * @param string $sPath1
	 * @param string $sPath2
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public function isEqualFilesSha1( $sPath1, $sPath2 ) {

		if ( !Services::WpFs()->isFile( $sPath2 ) ) {
			throw new \InvalidArgumentException( 'File does not exist on disk to compare' );
		}

		$oDataManip = Services::DataManipulation();
		return
			$this->isEqualFileSha1(
				$sPath1,
				sha1( $oDataManip->convertLineEndingsDosToLinux( $sPath2 ) )
			)
			|| $this->isEqualFileSha1(
				$sPath1,
				sha1( $oDataManip->convertLineEndingsLinuxToDos( $sPath2 ) )
			);
	}
}