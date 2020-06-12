<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\BaseShield;

use FernleafSystems\Wordpress\Plugin\Shield\Modules\Base;
use FernleafSystems\Wordpress\Services\Services;

class ShieldProcessor extends Base\BaseProcessor {

	const RECAPTCHA_JS_HANDLE = 'icwp-google-recaptcha';

	/**
	 * @var bool
	 */
	private static $bRecaptchaEnqueue = false;

	/**
	 * Resets the object values to be re-used anew
	 */
	public function init() {
		parent::init();
		$oFO = $this->getMod();
		add_filter( $oFO->prefix( 'collect_tracking_data' ), [ $this, 'tracking_DataCollect' ] );
	}

	/**
	 * @param \WP_User $oUser
	 * @return bool
	 */
	protected function isUserSubjectToLoginIntent( $oUser = null ) {
		$bIsSubject = false;

		if ( !$oUser instanceof \WP_User ) {
			$oUser = Services::WpUsers()->getCurrentWpUser();
		}
		if ( $oUser instanceof \WP_User ) {
			$bIsSubject = apply_filters( $this->getCon()->prefix( 'user_subject_to_login_intent' ), false, $oUser );
		}

		return $bIsSubject;
	}

	/**
	 * Filter used to collect plugin data for tracking.  Fired from the plugin processor only if the option is enabled
	 * - it is not enabled by default.
	 * Note that in this case we "mask" options that have been identified as "sensitive" - i.e. could contain
	 * identifiable data.
	 *
	 * @param $aData
	 * @return array
	 */
	public function tracking_DataCollect( $aData ) {
		if ( !is_array( $aData ) ) {
			$aData = [];
		}
		$oMod = $this->getMod();
		$aOptions = $oMod->collectOptionsForTracking();
		if ( !empty( $aOptions ) ) {
			$aData[ $oMod->getSlug() ] = [ 'options' => $oMod->collectOptionsForTracking() ];
		}
		return $aData;
	}

	/**
	 * @throws \Exception
	 * @deprecated 9.0
	 */
	public function maybeDequeueRecaptcha() {
	}

	/**
	 * @deprecated 9.0
	 */
	public function registerGoogleRecaptchaJs() {
	}

	/**
	 * @deprecated 9.0
	 */
	public function isRecaptchaEnqueue() {
		return false;
	}

	/**
	 * Note we don't provide a 'false' option here as if it's set to be needed somewhere,
	 * it shouldn't be unset anywhere else.
	 *
	 * @return $this
	 */
	public function setRecaptchaToEnqueue() {
		return $this;
	}

	/**
	 * @return bool
	 * @deprecated 9.0
	 */
	protected function getRecaptchaTheme() {
		return 'light';
	}

	/**
	 * @return bool
	 * @deprecated 9.0
	 */
	protected function isRecaptchaInvisible() {
		return false;
	}
}