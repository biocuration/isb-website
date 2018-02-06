<?php

namespace FernleafSystems\Utilities\Data\Adapter;

/**
 * Trait StdClassAdapter
 * @package FernleafSystems\Utilities\Data\Adapter
 */
trait StdClassAdapter {

	/**
	 * @var \stdClass
	 */
	private $oRaw;

	/**
	 * @param string $sProperty
	 * @return mixed
	 */
	public function __get( $sProperty ) {
		return $this->getParam( $sProperty );
	}

	/**
	 * @param string $sProperty
	 * @param mixed $mValue
	 * @return $this
	 */
	public function __set( $sProperty, $mValue ) {
		return $this->setParam( $sProperty, $mValue );
	}

	/**
	 * @param array $aDataValues associative with parameter keys and values
	 * @param array $aRestrictedKeys
	 * @return $this
	 */
	public function applyFromArray( $aDataValues, $aRestrictedKeys = array() ) {
		if ( !empty( $aRestrictedKeys ) ) {
			$aDataValues = array_intersect_key( $aDataValues, array_flip( $aRestrictedKeys ) );
		}
		$this->reset();
		foreach ( $aDataValues as $sKey => $mValue ) {
			$this->setParam( $sKey, $mValue );
		}
		return $this;
	}

	/**
	 * @return $this
	 */
	public function reset() {
		$this->oRaw = new \stdClass();
		return $this;
	}

	/**
	 * @param bool $bClone
	 * @return \stdClass
	 */
	public function getRawData( $bClone = false ) {
		if ( !is_object( $this->oRaw ) ) {
			$this->oRaw = new \stdClass();
		}
		return $bClone ? clone $this->oRaw : $this->oRaw;
	}

	/**
	 * @return array
	 */
	public function getRawDataAsArray() {
		return (array)$this->getRawData();
	}

	/**
	 * @param string $sKey
	 * @param mixed $mComparison
	 * @return bool
	 */
	public function isParam( $sKey, $mComparison ) {
		return ( $this->getParam( $sKey ) == $mComparison );
	}

	/**
	 * @param string $sKey
	 * @param array $nDefault
	 * @return array
	 */
	public function getArrayParam( $sKey, $nDefault = array() ) {
		$aVal = $this->getParam( $sKey, $nDefault );
		return ( !is_null( $aVal ) && is_array( $aVal ) ) ? $aVal : $nDefault;
	}

	/**
	 * @param string $sKey
	 * @param int $nDefault
	 * @return int|float|null
	 */
	public function getNumericParam( $sKey, $nDefault = null ) {
		$nVal = $this->getParam( $sKey, $nDefault );
		return ( !is_null( $nVal ) && is_numeric( $nVal ) ) ? $nVal : $nDefault;
	}

	/**
	 * @param string $sKey
	 * @param mixed $mDefault
	 * @return mixed
	 */
	public function getParam( $sKey, $mDefault = null ) {
		return isset( $this->getRawData()->{$sKey} ) ? $this->getRawData()->{$sKey} : $mDefault;
	}

	/**
	 * @param string $sKey
	 * @param string $mDefault
	 * @return string
	 */
	public function getStringParam( $sKey, $mDefault = '' ) {
		$sVal = $this->getParam( $sKey, $mDefault );
		return ( !is_null( $sVal ) && is_string( $sVal ) ) ? trim( $sVal ) : $mDefault;
	}

	/**
	 * @param object $oRaw
	 * @return $this
	 */
	public function setRawData( $oRaw ) {
		$this->oRaw = $oRaw;
		return $this;
	}

	/**
	 * @param string $sKey
	 * @param mixed $mValue
	 * @return $this
	 */
	public function setParam( $sKey, $mValue ) {
		$this->getRawData()->{$sKey} = $mValue;
		return $this;
	}

	/**
	 * @alias TODO remove
	 * @param string $sKey
	 * @param mixed $mValue
	 * @return $this
	 */
	public function setRawDataItem( $sKey, $mValue ) {
		return $this->setParam( $sKey, $mValue );
	}
}