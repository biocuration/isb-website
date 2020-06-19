<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Options;

use FernleafSystems\Wordpress\Services\Core\System;
use FernleafSystems\Wordpress\Services\Services;

/**
 * Remarkably, it seems that some WordPress sites can't actually store WordPress Transients
 * so we run a quick test to see.
 *
 * Class Transient
 * @package FernleafSystems\Wordpress\Services\Utilities\Options
 */
class TestCanUseTransients {

	/**
	 * @var bool
	 */
	private static $bCan;

	/**
	 * @return bool
	 */
	public function run() {
		if ( isset( self::$bCan ) ) {
			return self::$bCan;
		}

		$oWP = Services::WpGeneral();
		$sOptPrefix = System::PREFIX.'can_trans_';
		$mCan = $oWP->getOption( $sOptPrefix.'confirmed', false, true );

		if ( !in_array( $mCan, [ 'Y', 'N' ] ) ) {
			$nStartedAt = $oWP->getOption( $sOptPrefix.'started', false, true );
			if ( is_numeric( $nStartedAt ) && $nStartedAt > 0 ) {
				$sTransResult = $oWP->getTransient( $sOptPrefix.'test' );
				if ( $sTransResult === System::PREFIX.'test_value' ) {
					$mCan = 'Y';
				}
				else {
					$mCan = 'N';
				}
				$oWP->deleteOption( $sOptPrefix.'started', true );
				$oWP->updateOption( $sOptPrefix.'confirmed', $mCan, true );
			}
			else {
				$mCan = 'Y'; // We temporarily state that we can use transients
				$oWP->setTransient( $sOptPrefix.'test', System::PREFIX.'test_value', 3600 );
				$oWP->updateOption( $sOptPrefix.'started', Services::Request()->ts(), true );
			}
		}

		self::$bCan = ( $mCan === 'Y' );
		return self::$bCan;
	}
}