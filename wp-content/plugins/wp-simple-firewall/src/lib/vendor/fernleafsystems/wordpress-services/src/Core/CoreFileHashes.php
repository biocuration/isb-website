<?php

namespace FernleafSystems\Wordpress\Services\Core;

use FernleafSystems\Wordpress\Services\Services;
use FernleafSystems\Wordpress\Services\Utilities\File\Compare\CompareHash;

/**
 * Class CoreFileHashes
 * @package FernleafSystems\Wordpress\Services\Core
 */
class CoreFileHashes {

	/**
	 * @var array
	 */
	private $aHashes;

	/**
	 * Filters out wp-content plugins/themes data.
	 * @return array
	 */
	public function getHashes() {
		if ( !isset( $this->aHashes ) ) {
			$aHashes = Services::WpGeneral()->getCoreChecksums();

			$this->aHashes = array_intersect_key(
				$aHashes,
				array_flip( array_filter(
					array_keys( $aHashes ),
					function ( $sFilePath ) {
						return preg_match( '#wp-content/(plugins|themes)#i', $sFilePath ) === 0;
					}
				) )
			);
		}
		return $this->aHashes;
	}

	/**
	 * @param string $sFile
	 * @return string|null
	 */
	public function getFileHash( $sFile ) {
		$sNorm = $this->getFileFragment( $sFile );
		return $this->isCoreFile( $sNorm ) ? $this->getHashes()[ $sNorm ] : null;
	}

	/**
	 * @param string $sFile
	 * @return string
	 */
	public function getFileFragment( $sFile ) {
		return Services::WpFs()->getPathRelativeToAbsPath( $sFile );
	}

	/**
	 * @param string $sFile
	 * @return string
	 */
	public function getAbsolutePathFromFragment( $sFile ) {
		return wp_normalize_path( path_join( ABSPATH, $this->getFileFragment( $sFile ) ) );
	}

	/**
	 * @param string $sFile
	 * @return bool
	 */
	public function isCoreFile( $sFile ) {
		return array_key_exists( $this->getFileFragment( $sFile ), $this->getHashes() );
	}

	/**
	 * @param string $sFullPath
	 * @return bool
	 */
	public function isCoreFileHashValid( $sFullPath ) {
		try {
			$bValid = $this->isCoreFile( $sFullPath )
					  && ( new CompareHash() )->isEqualFileMd5( $sFullPath, $this->getFileHash( $sFullPath ) );
		}
		catch ( \Exception $oE ) {
			$bValid = false;
		}
		return $bValid;
	}

	/**
	 * @return bool
	 */
	public function isReady() {
		return ( count( $this->getHashes() ) > 0 );
	}
}