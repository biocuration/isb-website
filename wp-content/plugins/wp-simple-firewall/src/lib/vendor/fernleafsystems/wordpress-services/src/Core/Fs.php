<?php

namespace FernleafSystems\Wordpress\Services\Core;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class Fs
 * @package FernleafSystems\Wordpress\Services\Core
 */
class Fs {

	/**
	 * @var \WP_Filesystem_Base
	 */
	protected $oWpfs = null;

	/**
	 * @param string $sPath
	 * @return bool
	 */
	public function isAbsPath( $sPath ) {
		return path_is_absolute( $sPath ) ||
			   ( Services::Data()->isWindows() && preg_match( '#^[a-zA-Z]:/{1,2}#', wp_normalize_path( $sPath ) ) === 1 );
	}

	/**
	 * @param string $sBase
	 * @param string $sPath
	 * @return string
	 */
	public function pathJoin( $sBase, $sPath ) {
		return rtrim( $sBase, DIRECTORY_SEPARATOR ).DIRECTORY_SEPARATOR.ltrim( $sPath, DIRECTORY_SEPARATOR );
	}

	/**
	 * @param string $sDir
	 * @param array  $aExclude
	 */
	public function emptyDir( $sDir, $aExclude = [] ) {
		if ( $this->exists( $sDir ) ) {
			foreach ( new \DirectoryIterator( $sDir ) as $oFile ) {
				/** @var $oFile \DirectoryIterator */
				if ( !$oFile->isDot() && !in_array( $oFile->getBasename(), $aExclude ) ) {
					$oFile->isDir() ? $this->deleteDir( $oFile->getPathname() ) : $this->deleteFile( $oFile->getPathname() );
				}
			}
		}
		else {
			$this->mkdir( $sDir );
		}
	}

	/**
	 * @param string $sSource
	 * @param string $sTarget
	 */
	public function moveDirContents( $sSource, $sTarget ) {

		if ( !$this->exists( $sTarget ) ) {
			$this->mkdir( $sTarget );
		}

		$oDirIt = new \DirectoryIterator( $sSource );
		foreach ( $oDirIt as $oFile ) {
			if ( !$oFile->isDot() ) {
				$this->move( $oFile->getPathname(), path_join( $sTarget, $oFile->getBasename() ) );
			}
		}
	}

	/**
	 * @param $sFilePath
	 * @return bool|null    true/false whether file/directory exists
	 */
	public function exists( $sFilePath ) {
		$oFs = $this->getWpfs();
		if ( $oFs && $oFs->exists( $sFilePath ) ) {
			return true;
		}
		return function_exists( 'file_exists' ) ? file_exists( $sFilePath ) : null;
	}

	/**
	 * @param string $sNeedle
	 * @param string $sDir
	 * @param bool   $bIncludeExtension
	 * @param bool   $bCaseSensitive
	 * @return string|null
	 */
	public function findFileInDir( $sNeedle, $sDir, $bIncludeExtension = true, $bCaseSensitive = false ) {
		if ( empty( $sNeedle ) || empty( $sDir ) || !$this->canAccessDirectory( $sDir ) ) {
			return false;
		}

		$aAllFiles = $this->getAllFilesInDir( $sDir, false );
		if ( !$bCaseSensitive ) {
			$sNeedle = strtolower( $sNeedle );
			$aAllFiles = array_map( 'strtolower', $aAllFiles );
		}

		//if the file you're searching for doesn't have an extension, then we don't include extensions in search
		$nDotPosition = strpos( $sNeedle, '.' );
		$bHasExtension = $nDotPosition !== false;
		$bIncludeExtension = $bIncludeExtension && $bHasExtension;
		$sNeedlePreExtension = $bHasExtension ? substr( $sNeedle, 0, $nDotPosition ) : $sNeedle;

		$sTheFile = null;
		foreach ( $aAllFiles as $sFilename ) {

			$sFilePart = basename( $sFilename );
			if ( $bIncludeExtension ) {
				if ( $sFilePart == $sNeedle ) {
					$sTheFile = $sFilename;
					break;
				}
			}
			elseif ( strpos( $sFilePart, $sNeedlePreExtension ) === 0 ) {
				// This is not entirely accurate as it only finds whether a file "starts" with needle, ignoring subsequent characters
				$sTheFile = $sFilename;
				break;
			}
		}

		return $sTheFile;
	}

	/**
	 * @param string $sDir
	 * @return bool
	 */
	protected function canAccessDirectory( $sDir ) {
		return !is_null( $this->getDirIterator( $sDir ) );
	}

	/**
	 * @param string $sDir
	 * @param bool   $bIncludeDirs
	 * @return string[]
	 */
	public function getAllFilesInDir( $sDir, $bIncludeDirs = true ) {
		$aFiles = [];
		if ( $this->canAccessDirectory( $sDir ) ) {
			foreach ( $this->getDirIterator( $sDir ) as $oFileItem ) {
				if ( !$oFileItem->isDot() && ( $oFileItem->isFile() || $bIncludeDirs ) ) {
					$aFiles[] = $oFileItem->getPathname();
				}
			}
		}
		return empty( $aFiles ) ? [] : $aFiles;
	}

	/**
	 * @param string $sDir
	 * @return \DirectoryIterator|null
	 */
	protected function getDirIterator( $sDir ) {
		try {
			$oIterator = new \DirectoryIterator( $sDir );
		}
		catch ( \Exception $oE ) { //  UnexpectedValueException, RuntimeException, Exception
			$oIterator = null;
		}
		return $oIterator;
	}

	/**
	 * @return string|null
	 */
	public function getContent_WpConfig() {
		return $this->getFileContent( Services::WpGeneral()->getPath_WpConfig() );
	}

	/**
	 * @param string $sContent
	 * @return bool
	 */
	public function putContent_WpConfig( $sContent ) {
		return $this->putFileContent( Services::WpGeneral()->getPath_WpConfig(), $sContent );
	}

	/**
	 * @param string $sUrl
	 * @param bool   $bSecure
	 * @return bool
	 */
	public function getIsUrlValid( $sUrl, $bSecure = false ) {
		$sSchema = $bSecure ? 'https://' : 'http://';
		$sUrl = ( strpos( $sUrl, 'http' ) !== 0 ) ? $sSchema.$sUrl : $sUrl;
		return Services::HttpRequest()->get( $sUrl );
	}

	/**
	 * @return bool
	 */
	public function getCanWpRemoteGet() {
		$bCan = false;
		$aUrlsToTest = [
			'https://www.microsoft.com',
			'https://www.google.com',
			'https://www.facebook.com'
		];
		foreach ( $aUrlsToTest as $sUrl ) {
			if ( Services::HttpRequest()->get( $sUrl ) ) {
				$bCan = true;
				break;
			}
		}
		return $bCan;
	}

	public function getCanDiskWrite() {
		$sFilePath = __DIR__.'/testfile.'.rand().'txt';
		$sContents = "Testing icwp file read and write.";

		// Write, read, verify, delete.
		if ( $this->putFileContent( $sFilePath, $sContents ) ) {
			$sFileContents = $this->getFileContent( $sFilePath );
			if ( !is_null( $sFileContents ) && $sFileContents === $sContents ) {
				return $this->deleteFile( $sFilePath );
			}
		}
		return false;
	}

	/**
	 * @param $sPath
	 * @return string
	 */
	public function getPathRelativeToAbsPath( $sPath ) {
		return preg_replace(
			sprintf( '#^%s#i', preg_quote( wp_normalize_path( ABSPATH ), '#' ) ),
			'',
			wp_normalize_path( $sPath )
		);
	}

	/**
	 * @param string $sFilePath
	 * @return int|null
	 */
	public function getModifiedTime( $sFilePath ) {
		return $this->getTime( $sFilePath, 'modified' );
	}

	/**
	 * @param string $sFilePath
	 * @return int|null
	 */
	public function getAccessedTime( $sFilePath ) {
		return $this->getTime( $sFilePath, 'accessed' );
	}

	/**
	 * @param string $sFilePath
	 * @param string $sProperty
	 * @return int|null
	 */
	public function getTime( $sFilePath, $sProperty = 'modified' ) {

		if ( !$this->exists( $sFilePath ) ) {
			return null;
		}

		$oFs = $this->getWpfs();
		switch ( $sProperty ) {

			case 'modified' :
				return $oFs ? $oFs->mtime( $sFilePath ) : filemtime( $sFilePath );
				break;
			case 'accessed' :
				return $oFs ? $oFs->atime( $sFilePath ) : fileatime( $sFilePath );
				break;
			default:
				return null;
				break;
		}
	}

	/**
	 * @param string $sFilePath
	 * @return null|bool
	 */
	public function getCanReadWriteFile( $sFilePath ) {
		if ( !file_exists( $sFilePath ) ) {
			return null;
		}

		$nFileSize = filesize( $sFilePath );
		if ( $nFileSize === 0 ) {
			return null;
		}

		$sFileContent = $this->getFileContent( $sFilePath );
		if ( empty( $sFileContent ) ) {
			return false; //can't even read the file!
		}
		return $this->putFileContent( $sFilePath, $sFileContent );
	}

	/**
	 * @param string $sFilePath
	 * @param bool   $bIsCompressed
	 * @return string|null
	 */
	public function getFileContent( $sFilePath, $bIsCompressed = false ) {
		$sContents = null;
		$oFs = $this->getWpfs();
		if ( $oFs ) {
			$sContents = $oFs->get_contents( $sFilePath );
		}

		if ( empty( $sContents ) && function_exists( 'file_get_contents' ) ) {
			$sContents = file_get_contents( $sFilePath );
		}

		if ( !empty( $sContents ) && $bIsCompressed && function_exists( 'gzinflate' ) ) {
			$sContents = gzinflate( $sContents );
		}

		return $sContents;
	}

	/**
	 * Use this to reliably read the contents of a PHP file that doesn't have executable
	 * PHP Code.
	 * Why use this? In the name of naive security, silly web hosts can prevent reading the contents of
	 * non-PHP files so we simply put the content we want to have read into a php file and then "include" it.
	 * @param string $sFile
	 * @return string
	 */
	public function getFileContentUsingInclude( $sFile ) {
		ob_start();
		@include( $sFile );
		return ob_get_clean();
	}

	/**
	 * @param $sFilePath
	 * @return bool
	 */
	public function getFileSize( $sFilePath ) {
		$oFs = $this->getWpfs();
		if ( $oFs && ( $oFs->size( $sFilePath ) > 0 ) ) {
			return $oFs->size( $sFilePath );
		}
		return @filesize( $sFilePath );
	}

	/**
	 * @param string                      $sDir
	 * @param int                         $nMaxDepth - set to zero for no max
	 * @param \RecursiveDirectoryIterator $oDirIterator
	 * @return \SplFileInfo[]
	 */
	public function getFilesInDir( $sDir, $nMaxDepth = 1, $oDirIterator = null ) {
		$aList = [];

		try {
			if ( empty( $oDirIterator ) ) {
				$oDirIterator = new \RecursiveDirectoryIterator( $sDir );
				if ( method_exists( $oDirIterator, 'setFlags' ) ) {
					$oDirIterator->setFlags( \RecursiveDirectoryIterator::SKIP_DOTS );
				}
			}

			$oRecurIter = new \RecursiveIteratorIterator( $oDirIterator );
			$oRecurIter->setMaxDepth( $nMaxDepth - 1 ); //since they start at zero.

			/** @var \SplFileInfo $oFile */
			foreach ( $oRecurIter as $oFile ) {
				$aList[] = clone $oFile;
			}
		}
		catch ( \Exception $oE ) { //  UnexpectedValueException, RuntimeException, Exception
		}

		return $aList;
	}

	/**
	 * @param string|null $sBaseDir
	 * @param string      $sPrefix
	 * @param string      $outsRandomDir
	 * @return bool|string
	 */
	public function getTempDir( $sBaseDir = null, $sPrefix = '', &$outsRandomDir = '' ) {
		$sTemp = rtrim( ( is_null( $sBaseDir ) ? get_temp_dir() : $sBaseDir ), DIRECTORY_SEPARATOR ).DIRECTORY_SEPARATOR;

		$sCharset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz0123456789';
		do {
			$sDir = $sPrefix;
			for ( $i = 0 ; $i < 8 ; $i++ ) {
				$sDir .= $sCharset[ ( rand()%strlen( $sCharset ) ) ];
			}
		} while ( is_dir( $sTemp.$sDir ) );

		$outsRandomDir = $sDir;

		$bSuccess = true;
		if ( !@mkdir( $sTemp.$sDir, 0755, true ) ) {
			$bSuccess = false;
		}
		return ( $bSuccess ? $sTemp.$sDir : false );
	}

	/**
	 * @param string $sFilePath
	 * @param string $sContents
	 * @param bool   $bCompressed
	 * @return bool
	 */
	public function putFileContent( $sFilePath, $sContents, $bCompressed = false ) {

		if ( $bCompressed && function_exists( 'gzdeflate' ) ) {
			$sContents = gzdeflate( $sContents );
		}

		$oFs = $this->getWpfs();
		if ( $oFs && $oFs->put_contents( $sFilePath, $sContents, FS_CHMOD_FILE ) ) {
			return true;
		}

		if ( function_exists( 'file_put_contents' ) ) {
			return file_put_contents( $sFilePath, $sContents ) !== false;
		}
		return false;
	}

	/**
	 * Recursive delete
	 * @param string $sDir
	 * @return bool
	 */
	public function deleteDir( $sDir ) {
		$oFs = $this->getWpfs();
		if ( $oFs && $oFs->rmdir( $sDir, true ) ) {
			return true;
		}
		return @rmdir( $sDir );
	}

	/**
	 * @param string $sFilePath
	 * @return bool|null
	 */
	public function deleteFile( $sFilePath ) {
		$oFs = $this->getWpfs();
		if ( $oFs && $oFs->delete( $sFilePath ) ) {
			return true;
		}
		return function_exists( 'unlink' ) ? @unlink( $sFilePath ) : null;
	}

	/**
	 * @param string $sFilePathSource
	 * @param string $sFilePathDestination
	 * @return bool|null
	 */
	public function move( $sFilePathSource, $sFilePathDestination ) {
		$oFs = $this->getWpfs();
		if ( $oFs && $oFs->move( $sFilePathSource, $sFilePathDestination ) ) {
			return true;
		}
		return function_exists( 'rename' ) ? @rename( $sFilePathSource, $sFilePathDestination ) : null;
	}

	/**
	 * @param string $sPath
	 * @return bool
	 */
	public function isDir( $sPath ) {
		$oFs = $this->getWpfs();
		if ( $oFs && $oFs->is_dir( $sPath ) ) {
			return true;
		}
		return function_exists( 'is_dir' ) ? is_dir( $sPath ) : false;
	}

	/**
	 * @param $sPath
	 * @return bool|mixed
	 */
	public function isFile( $sPath ) {
		$oFs = $this->getWpfs();
		if ( $oFs && $oFs->is_file( $sPath ) ) {
			return true;
		}
		return function_exists( 'is_file' ) ? is_file( $sPath ) : null;
	}

	/**
	 * @return bool
	 */
	public function isFilesystemAccessDirect() {
		return ( $this->getWpfs() instanceof \WP_Filesystem_Direct );
	}

	/**
	 * @param string $sPath
	 * @return bool
	 */
	public function mkdir( $sPath ) {
		return wp_mkdir_p( $sPath );
	}

	/**
	 * @param string $sFilePath
	 * @param int    $nTime
	 * @return bool|mixed
	 */
	public function touch( $sFilePath, $nTime = null ) {
		$oFs = $this->getWpfs();
		if ( $oFs && $oFs->touch( $sFilePath, $nTime ) ) {
			return true;
		}
		return function_exists( 'touch' ) ? @touch( $sFilePath, $nTime ) : null;
	}

	/**
	 * @return \WP_Filesystem_Base
	 */
	protected function getWpfs() {
		if ( is_null( $this->oWpfs ) ) {
			$this->initFileSystem();
		}
		return $this->oWpfs;
	}

	/**
	 */
	private function initFileSystem() {
		if ( is_null( $this->oWpfs ) ) {
			$this->oWpfs = false;
			require_once( ABSPATH.'wp-admin/includes/file.php' );
			if ( \WP_Filesystem() ) {
				global $wp_filesystem;
				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
					$this->oWpfs = $wp_filesystem;
				}
			}
		}
	}

	/**
	 * @param string $sUrl
	 * @param array  $aRequestArgs
	 * @return array|bool
	 * @deprecated
	 */
	public function requestUrl( $sUrl, $aRequestArgs = [] ) {
		return Services::HttpRequest()->requestUrl( $sUrl, $aRequestArgs );
	}

	/**
	 * @param string $sUrl
	 * @param array  $aRequestArgs
	 * @return array|false
	 * @deprecated
	 */
	public function getUrl( $sUrl, $aRequestArgs = [] ) {
		return Services::HttpRequest()->requestUrl( $sUrl, $aRequestArgs, 'GET' );
	}

	/**
	 * @param string $sUrl
	 * @param array  $aRequestArgs
	 * @return false|string
	 * @deprecated
	 */
	public function getUrlContent( $sUrl, $aRequestArgs = [] ) {
		return Services::HttpRequest()->getContent( $sUrl, $aRequestArgs );
	}

	/**
	 * @param string $sUrl
	 * @param array  $aRequestArgs
	 * @return array|false
	 * @deprecated
	 */
	public function postUrl( $sUrl, $aRequestArgs = [] ) {
		return Services::HttpRequest()->requestUrl( $sUrl, $aRequestArgs, 'POST' );
	}

	/**
	 * @return string
	 * @deprecated
	 */
	public function getWpConfigPath() {
		return Services::WpGeneral()->getPath_WpConfig();
	}
}