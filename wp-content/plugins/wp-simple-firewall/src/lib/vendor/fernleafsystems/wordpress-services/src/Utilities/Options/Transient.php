<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Options;

use FernleafSystems\Wordpress\Services\Core\System;
use FernleafSystems\Wordpress\Services\Services;

/**
 * Remarkably, it seems that some WordPress sites can't actually store WordPress Transients.
 * Class Transient
 * @package FernleafSystems\Wordpress\Services\Utilities\Options
 */
class Transient {

	/**
	 * @param string $sKey
	 * @param bool   $bIgnoreWPMS
	 * @return bool
	 */
	public static function Delete( $sKey, $bIgnoreWPMS = true ) {
		$oWP = Services::WpGeneral();
		return $oWP->canUseTransients() ?
			$oWP->deleteTransient( $sKey )
			: Services::WpGeneral()->deleteOption( System::PREFIX.'trans_'.$sKey, $bIgnoreWPMS );
	}

	/**
	 * @param string $sKey
	 * @param null   $mDefault
	 * @param bool   $bIgnoreWPMS
	 * @return mixed|null
	 */
	public static function Get( $sKey, $mDefault = null, $bIgnoreWPMS = true ) {
		$mVal = null;

		$oWP = Services::WpGeneral();

		if ( $oWP->canUseTransients() ) {
			$mVal = $oWP->getTransient( $sKey );
		}
		else {
			$aData = $oWP->getOption( System::PREFIX.'trans_'.$sKey, null, $bIgnoreWPMS );
			if ( !empty( $aData ) && is_array( $aData ) && isset( $aData[ 'data' ] )
				 && isset( $aData[ 'expires_at' ] ) ) {
				if ( $aData[ 'expires_at' ] === 0 || Services::Request()->ts() < $aData[ 'expires_at' ] ) {
					$mVal = $aData[ 'data' ];
				}
			}
		}

		return is_null( $mVal ) ? $mDefault : $mVal;
	}

	/**
	 * @param string $sKey
	 * @param mixed  $mData
	 * @param int    $nLifeTime
	 * @param bool   $bIgnoreWPMS
	 * @return bool
	 */
	public static function Set( $sKey, $mData, $nLifeTime = 0, $bIgnoreWPMS = true ) {
		if ( is_null( $mData ) ) {
			self::Delete( $sKey );
		}

		$oWP = Services::WpGeneral();

		if ( $oWP->canUseTransients() ) {
			return $oWP->setTransient( $sKey, $mData, $nLifeTime );
		}
		else {
			return $oWP->updateOption(
				System::PREFIX.'trans_'.$sKey,
				[
					'data'       => $mData,
					'expires_at' => empty( $nLifeTime ) ? 0 : Services::Request()->ts() + max( 0, $nLifeTime ),
				],
				$bIgnoreWPMS
			);
		}
	}
}
