<?php

namespace FernleafSystems\Wordpress\Services\Utilities\File;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class LocateStrInFile
 * @package FernleafSystems\Wordpress\Services\Utilities\File
 */
class LocateStrInFile {

	/**
	 * @var string
	 */
	private $sNeedle;

	/**
	 * @var string
	 */
	private $sContent;

	/**
	 * @var string
	 */
	private $sPath;

	/**
	 * @var string[]
	 */
	private $aLines;

	/**
	 * @var bool
	 */
	private $bIsRegExNeedle;

	/**
	 * @return string[]
	 */
	public function run() {
		return $this->isRegEx() ? $this->runAsRegEx() : $this->runAsSimple();
	}

	/**
	 * @return string[] - keys are line numbers
	 */
	protected function runAsRegEx() {
		$sNeedle = $this->getNeedle();
		return array_filter(
			$this->getLines(),
			function ( $sLine ) use ( $sNeedle ) {
				return preg_match( '/'.$sNeedle.'/im', $sLine );
			}
		);
	}

	/**
	 * @return string[] - keys are line numbers
	 */
	protected function runAsSimple() {
		$aLines = [];
		$sNeedle = $this->getNeedle();
		if ( stripos( $this->getContent(), $this->getNeedle() ) !== false ) {
			$aLines = array_filter(
				$this->getLines(),
				function ( $sLine ) use ( $sNeedle ) {
					return ( strpos( $sLine, $sNeedle ) !== false );
				}
			);
		}
		return $aLines;
	}

	/**
	 * @param $sPath
	 * @return int[]
	 * @throws \InvalidArgumentException
	 * @deprecated
	 */
	public function inFile( $sPath ) {
		return $this->setPath( $sPath )
					->run();
	}

	/**
	 * @return string[]
	 */
	protected function getLines() {
		if ( is_null( $this->aLines ) ) {
			$this->aLines = explode( "\n", $this->getContent() );
		}
		return $this->aLines;
	}

	/**
	 * @return string
	 */
	public function getContent() {
		if ( is_null( $this->sContent ) ) {
			$this->sContent = Services::WpFs()->getFileContent( $this->getPath() );
		}
		return $this->sContent;
	}

	/**
	 * @return string
	 */
	public function getNeedle() {
		return $this->sNeedle;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->sPath;
	}

	/**
	 * @return bool
	 */
	public function isRegEx() {
		return (bool)$this->bIsRegExNeedle;
	}

	/**
	 * @param bool $bIsRegEx
	 * @return $this
	 */
	public function setIsRegEx( $bIsRegEx ) {
		$this->bIsRegExNeedle = $bIsRegEx;
		return $this;
	}

	/**
	 * @param string $sStr
	 * @return $this
	 */
	public function setNeedle( $sStr ) {
		$this->sNeedle = $sStr;
		return $this;
	}

	/**
	 * @param string $sPath
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setPath( $sPath ) {
		if ( !Services::WpFs()->isFile( $sPath ) ) {
			throw new \InvalidArgumentException( 'File does not exist' );
		}
		$this->sPath = $sPath;
		return $this->reset();
	}

	/**
	 * @return $this
	 */
	protected function reset() {
		$this->sContent = null;
		$this->aLines = null;
		return $this;
	}
}