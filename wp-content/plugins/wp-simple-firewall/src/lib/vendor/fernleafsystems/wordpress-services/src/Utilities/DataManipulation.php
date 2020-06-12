<?php

namespace FernleafSystems\Wordpress\Services\Utilities;

/**
 * Class DataManipulation
 * @package FernleafSystems\Wordpress\Services\Utilities
 */
class DataManipulation {

	/**
	 * @param string $sFullFilePath
	 * @return string
	 */
	public function convertLineEndingsDosToLinux( $sFullFilePath ) {
		return str_replace( [ "\r\n", "\r" ], "\n", file_get_contents( $sFullFilePath ) );
	}

	/**
	 * @param string $sFullFilePath
	 * @return string
	 */
	public function convertLineEndingsLinuxToDos( $sFullFilePath ) {
		return str_replace( "\n", "\r\n", $this->convertLineEndingsDosToLinux( $sFullFilePath ) );
	}

	/**
	 * @param array $aArrayToConvert
	 * @return string
	 */
	public function convertArrayToJavascriptDataString( $aArrayToConvert ) {
		$sParamsAsJs = '';
		foreach ( $aArrayToConvert as $sKey => $sValue ) {
			$sParamsAsJs .= sprintf( "'%s':'%s',", $sKey, $sValue );
		}
		return trim( $sParamsAsJs, ',' );
	}

	/**
	 * @param array $aArray
	 * @return \stdClass
	 */
	public function convertArrayToStdClass( $aArray ) {
		$oObject = new \stdClass();
		if ( !empty( $aArray ) && is_array( $aArray ) ) {
			foreach ( $aArray as $sKey => $mValue ) {
				$oObject->{$sKey} = $mValue;
			}
		}
		return $oObject;
	}

	/**
	 * @param \stdClass $oStdClass
	 * @return array
	 */
	public function convertStdClassToArray( $oStdClass ) {
		return json_decode( json_encode( $oStdClass ), true );
	}

	/**
	 * @param array    $aArray
	 * @param callable $cCallable
	 * @return array
	 */
	public function arrayMapRecursive( $aArray, $cCallable ) {
		$aMapped = [];
		foreach ( $aArray as $mKey => $mValue ) {
			if ( is_array( $mValue ) ) {
				$aMapped[ $mKey ] = $this->arrayMapRecursive( $mValue, $cCallable );
			}
			else {
				$aMapped[ $mKey ] = call_user_func( $cCallable, $mValue );
			}
		}
		return $aMapped;
	}

	/**
	 * @param mixed $args,...
	 * @return array
	 */
	public function mergeArraysRecursive( $args ) {
		$aArgs = array_values( array_filter( func_get_args(), 'is_array' ) );
		switch ( count( $aArgs ) ) {

			case 0:
				$aResult = [];
				break;

			case 1:
				$aResult = array_shift( $aArgs );
				break;

			case 2:
				list( $aResult, $aArray2 ) = $aArgs;
				foreach ( $aArray2 as $key => $Value ) {
					if ( !isset( $aResult[ $key ] ) ) {
						$aResult[ $key ] = $Value;
					}
//					elseif ( is_int( $key ) ) { behaviour is not as expected.
//						$aResult[] = $Value;
//					}
					elseif ( !is_array( $aResult[ $key ] ) || !is_array( $Value ) ) {
						$aResult[ $key ] = $Value;
					}
					else {
						$aResult[ $key ] = $this->mergeArraysRecursive( $aResult[ $key ], $Value );
					}
				}
				break;

			default:
				$aResult = array_shift( $aArgs );
				foreach ( $aArgs as $aArg ) {
					$aResult = $this->mergeArraysRecursive( $aResult, $aArg );
				}
				break;
		}

		return $aResult;
	}

	/**
	 * note: employs strict search comparison
	 * @param array $aArray
	 * @param mixed $mValue
	 * @param bool  $bFirstOnly - set true to only remove the first element found of this value
	 * @return array
	 */
	public function removeFromArrayByValue( $aArray, $mValue, $bFirstOnly = false ) {
		$aKeys = [];

		if ( $bFirstOnly ) {
			$mKey = array_search( $mValue, $aArray, true );
			if ( $mKey !== false ) {
				$aKeys[] = $mKey;
			}
		}
		else {
			$aKeys = array_keys( $aArray, $mValue, true );
		}

		foreach ( $aKeys as $mKey ) {
			unset( $aArray[ $mKey ] );
		}

		return $aArray;
	}

	/**
	 * @param array $aSubjectArray
	 * @param mixed $mValue
	 * @param int   $nDesiredPosition
	 * @return array
	 */
	public function setArrayValueToPosition( $aSubjectArray, $mValue, $nDesiredPosition ) {

		if ( $nDesiredPosition < 0 ) {
			return $aSubjectArray;
		}

		$nMaxPossiblePosition = count( $aSubjectArray ) - 1;
		if ( $nDesiredPosition > $nMaxPossiblePosition ) {
			$nDesiredPosition = $nMaxPossiblePosition;
		}

		$nPosition = array_search( $mValue, $aSubjectArray );
		if ( $nPosition !== false && $nPosition != $nDesiredPosition ) {

			// remove existing and reset index
			unset( $aSubjectArray[ $nPosition ] );
			$aSubjectArray = array_values( $aSubjectArray );

			// insert and update
			// http://stackoverflow.com/questions/3797239/insert-new-item-in-array-on-any-position-in-php
			array_splice( $aSubjectArray, $nDesiredPosition, 0, $mValue );
		}

		return $aSubjectArray;
	}

	/**
	 * @param array $aA
	 * @return array
	 */
	public function shuffleArray( $aA ) {
		$aKeys = array_keys( $aA );
		shuffle( $aKeys );
		return array_merge( array_flip( $aKeys ), $aA );
	}
}