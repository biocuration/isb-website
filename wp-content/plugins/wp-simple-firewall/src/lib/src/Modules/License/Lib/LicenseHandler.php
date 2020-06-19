<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\License\Lib;

use FernleafSystems\Wordpress\Plugin\Shield\License\EddLicenseVO;
use FernleafSystems\Wordpress\Plugin\Shield\Modules;
use FernleafSystems\Wordpress\Plugin\Shield\ShieldNetApi\HandshakingNonce;
use FernleafSystems\Wordpress\Services\Services;

class LicenseHandler {

	use Modules\ModConsumer;
	use Modules\Base\OneTimeExecute;

	protected function run() {
		$oCon = $this->getCon();

		add_action( $oCon->prefix( 'shield_action' ), function ( $sAction ) {
			$oCon = $this->getCon();
			switch ( $sAction ) {

				case 'keyless_handshake':
				case 'snapi_handshake':
					$sNonce = Services::Request()->query( 'nonce' );
					if ( !empty( $sNonce ) ) {
						die( json_encode( [
							'success' => ( new HandshakingNonce() )
								->setMod( $this->getMod() )
								->verify( $sNonce )
						] ) );
					}
					break;

				case 'license_check':
					if ( !wp_next_scheduled( $oCon->prefix( 'adhoc_cron_license_check' ) ) ) {
						wp_schedule_single_event( Services::Request()->ts() + 20, $oCon->prefix( 'adhoc_cron_license_check' ) );
					}
					break;
			}
		} );

		// performs the license check on-demand
		add_action( $oCon->prefix( 'adhoc_cron_license_check' ), function () {
			/** @var \ICWP_WPSF_FeatureHandler_License $oMod */
			$oMod = $this->getMod();
			try {
				$oMod->getLicenseHandler()->verify( true );
			}
			catch ( \Exception $oE ) {
			}
		} );
	}

	/**
	 * @return bool
	 */
	private function canCheck() {
		return !in_array( $this->getCon()->getShieldAction(), [ 'keyless_handshake', 'license_check' ] )
			   && $this->getIsLicenseNotCheckedFor( 20 )
			   && $this->canLicenseCheck_FileFlag();
	}

	/**
	 * @return $this
	 */
	public function clearLicense() {
		$this->getMod()->clearLastErrors();
		$this->getOptions()->setOpt( 'license_data', [] );
		return $this;
	}

	/**
	 * @param bool $bSendEmail
	 */
	public function deactivate( $bSendEmail = true ) {
		if ( $this->isActive() ) {
			$this->clearLicense();
			$this->getOptions()->setOptAt( 'license_deactivated_at' );
			if ( $bSendEmail ) {
				( new LicenseEmails() )
					->setMod( $this->getMod() )
					->sendLicenseDeactivatedEmail();
			}
			$this->getCon()->fireEvent( 'lic_fail_deactivate' );
		}
		// force all options to resave i.e. reset premium to defaults.
		add_filter( $this->getCon()->prefix( 'force_options_resave' ), '__return_true' );
	}

	/**
	 * @return int
	 */
	protected function getActivatedAt() {
		return $this->getOptions()->getOpt( 'license_activated_at' );
	}

	/**
	 * @return int
	 */
	protected function getDeactivatedAt() {
		return $this->getOptions()->getOpt( 'license_deactivated_at' );
	}

	/**
	 * @return EddLicenseVO
	 */
	public function getLicense() {
		$aData = $this->getOptions()->getOpt( 'license_data', [] );
		return ( new EddLicenseVO() )->applyFromArray( is_array( $aData ) ? $aData : [] );
	}

	/**
	 * @return int
	 */
	public function getLicenseNotCheckedForInterval() {
		return Services::Request()->ts() - $this->getOptions()->getOpt( 'license_last_checked_at' );
	}

	/**
	 * Use the grace period (currently 3 days) to adjust when the license registration
	 * expires on this site. We consider a registration as expired if the last verified
	 * date is past, or the actual license is expired - whichever happens earlier -
	 * plus the grace period.
	 * @return int
	 */
	public function getRegistrationExpiresAt() {
		/** @var \ICWP_WPSF_FeatureHandler_License $oMod */
		$oMod = $this->getMod();
		$oOpts = $this->getOptions();

		$nVerifiedExpiredDays = $oOpts->getDef( 'lic_verify_expire_days' )
								+ $oOpts->getDef( 'lic_verify_expire_grace_days' );

		$oLic = $oMod->getLicenseHandler()->getLicense();
		return (int)min(
			$oLic->getExpiresAt() + $oOpts->getDef( 'lic_verify_expire_grace_days' )*DAY_IN_SECONDS,
			$oLic->last_verified_at + $nVerifiedExpiredDays*DAY_IN_SECONDS
		);
	}

	/**
	 * IMPORTANT: Method used by Shield Central. Modify with care.
	 * We test various data points:
	 * 1) the key is valid format
	 * 2) the official license status is 'valid'
	 * 3) the license is marked as "active"
	 * 4) the license hasn't expired
	 * 5) the time since the last check hasn't expired
	 * @return bool
	 */
	public function hasValidWorkingLicense() {
		$oLic = $this->getLicense();
		return $oLic->isValid() && $this->isActive();
	}

	/**
	 * @return bool
	 */
	public function isActive() {
		return ( $this->getActivatedAt() > 0 )
			   && ( $this->getDeactivatedAt() < $this->getActivatedAt() );
	}

	/**
	 * @return bool
	 */
	public function isLastVerifiedExpired() {
		return ( Services::Request()->ts() - $this->getLicense()->last_verified_at )
			   > $this->getOptions()->getDef( 'lic_verify_expire_days' )*DAY_IN_SECONDS;
	}

	/**
	 * @return bool
	 */
	public function isLastVerifiedGraceExpired() {
		$oOpts = $this->getOptions();
		$nGracePeriod = ( $oOpts->getDef( 'lic_verify_expire_days' )
						  + $oOpts->getDef( 'lic_verify_expire_grace_days' ) )*DAY_IN_SECONDS;
		return ( Services::Request()->ts() - $this->getLicense()->last_verified_at ) > $nGracePeriod;
	}

	/**
	 * @return bool
	 */
	private function isMaybeExpiring() {
		return $this->isActive() &&
			   (
				   abs( Services::Request()->ts() - $this->getLicense()->getExpiresAt() )
				   < ( DAY_IN_SECONDS/2 )
			   );
	}

	/**
	 * @return bool
	 */
	public function isWithinVerifiedGraceExpired() {
		return $this->isLastVerifiedExpired() && !$this->isLastVerifiedGraceExpired();
	}

	/**
	 * @return bool
	 */
	private function isVerifyRequired() {
		return ( $this->isMaybeExpiring() && $this->getIsLicenseNotCheckedFor( HOUR_IN_SECONDS*4 ) )
			   || ( $this->isActive()
					&& !$this->getLicense()->isReady() && $this->getIsLicenseNotCheckedFor( HOUR_IN_SECONDS ) )
			   || ( $this->hasValidWorkingLicense() && $this->isLastVerifiedExpired()
					&& $this->getIsLicenseNotCheckedFor( HOUR_IN_SECONDS*4 ) );
	}

	/**
	 * @param bool $bForceCheck
	 * @return $this
	 * @throws \Exception
	 */
	public function verify( $bForceCheck = true ) {
		if ( $bForceCheck || ( $this->isVerifyRequired() && $this->canCheck() ) ) {
			( new Verify() )
				->setMod( $this->getMod() )
				->run();
		}
		return $this;
	}

	/**
	 * @param int $nTimePeriod
	 * @return bool
	 */
	private function getIsLicenseNotCheckedFor( $nTimePeriod ) {
		return $this->getLicenseNotCheckedForInterval() > $nTimePeriod;
	}

	/**
	 * @return bool
	 */
	private function canLicenseCheck_FileFlag() {
		$nMtime = (int)Services::WpFs()->getModifiedTime(
			$this->getCon()->getPath_Flags( 'license_check' )
		);
		return ( Services::Request()->ts() - $nMtime ) > MINUTE_IN_SECONDS;
	}
}
