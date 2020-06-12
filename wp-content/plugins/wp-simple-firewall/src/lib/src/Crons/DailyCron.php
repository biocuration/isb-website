<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Crons;

use FernleafSystems\Wordpress\Plugin\Shield;
use FernleafSystems\Wordpress\Services\Services;

class DailyCron extends BaseCron {

	/**
	 * @return string
	 */
	protected function getCronFrequency() {
		return 'daily';
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	protected function getCronName() {
		return $this->getCon()->prefix( 'daily' );
	}

	/**
	 * @return int
	 */
	public function getFirstRunTimestamp() {
		$nHour = apply_filters( $this->getCon()->prefix( 'daily_cron_hour' ), 7 );
		if ( $nHour < 0 || $nHour > 23 ) {
			$nHour = 7;
		}
		$oCarb = Services::Request()
						 ->carbon( true )
						 ->minute( 1 )
						 ->second( 0 );
		if ( $oCarb->hour >= $nHour ) {
			$oCarb->addDays( 1 );
		}
		return $oCarb->hour( $nHour )->timestamp;
	}

	/**
	 * Use the included action to hook into the plugin's daily cron
	 */
	public function runCron() {
		do_action( $this->getCon()->prefix( 'daily_cron' ) );
	}
}