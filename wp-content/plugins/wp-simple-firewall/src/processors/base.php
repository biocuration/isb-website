<?php

if ( class_exists( 'ICWP_WPSF_Processor_Base', false ) ) {
	return;
}

abstract class ICWP_WPSF_Processor_Base extends ICWP_WPSF_Foundation {

	/**
	 * @var ICWP_WPSF_FeatureHandler_Base
	 */
	protected $oFeatureOptions;

	/**
	 * @var int
	 */
	static protected $nPromoNoticesCount = 0;

	/**
	 * @var ICWP_WPSF_Processor_Base[]
	 */
	protected $aSubProcessors;

	/**
	 * @param ICWP_WPSF_FeatureHandler_Base $oFeatureOptions
	 */
	public function __construct( $oFeatureOptions ) {
		$this->oFeatureOptions = $oFeatureOptions;
		add_action( $oFeatureOptions->prefix( 'plugin_shutdown' ), array(
			$this,
			'action_doFeatureProcessorShutdown'
		) );
		add_action( $oFeatureOptions->prefix( 'generate_admin_notices' ), array( $this, 'autoAddToAdminNotices' ) );
		if ( method_exists( $this, 'addToAdminNotices' ) ) {
			add_action( $oFeatureOptions->prefix( 'generate_admin_notices' ), array( $this, 'addToAdminNotices' ) );
		}
		$this->init();
	}

	/**
	 * @return int
	 */
	protected function getPromoNoticesCount() {
		return self::$nPromoNoticesCount;
	}

	/**
	 * @return $this
	 */
	protected function incrementPromoNoticesCount() {
		self::$nPromoNoticesCount++;
		return $this;
	}

	/**
	 * @return ICWP_WPSF_Plugin_Controller
	 */
	public function getController() {
		return $this->getFeature()->getConn();
	}

	public function autoAddToAdminNotices() {
		$oCon = $this->getController();

		foreach ( $this->getFeature()->getAdminNotices() as $sNoticeId => $aAttrs ) {

			if ( !$this->getIfDisplayAdminNotice( $aAttrs ) ) {
				continue;
			}

			$sMethodName = 'addNotice_'.str_replace( '-', '_', $sNoticeId );
			if ( method_exists( $this, $sMethodName ) && isset( $aAttrs[ 'valid_admin' ] )
				 && $aAttrs[ 'valid_admin' ] && $oCon->getIsValidAdminArea() ) {

				$aAttrs[ 'id' ] = $sNoticeId;
				$aAttrs[ 'notice_id' ] = $sNoticeId;
				call_user_func( array( $this, $sMethodName ), $aAttrs );
			}
		}
	}

	/**
	 * @param array $aAttrs
	 * @return bool
	 */
	protected function getIfDisplayAdminNotice( $aAttrs ) {
		$oWpNotices = $this->loadAdminNoticesProcessor();

		if ( empty( $aAttrs[ 'schedule' ] )
			 || !in_array( $aAttrs[ 'schedule' ], array( 'once', 'conditions', 'version', 'never' ) ) ) {
			$aAttrs[ 'schedule' ] = 'conditions';
		}

		if ( $aAttrs[ 'schedule' ] == 'never' ) {
			return false;
		}

		if ( $aAttrs[ 'schedule' ] == 'once'
			 && ( !$this->loadWpUsers()->canSaveMeta() || $oWpNotices->isDismissed( $aAttrs[ 'id' ] ) )
		) {
			return false;
		}

		if ( isset( $aAttrs[ 'type' ] ) && $aAttrs[ 'type' ] == 'promo' ) {
			if ( $this->loadWp()->getIsMobile() ) {
				return false;
			}
		}

		return true;
	}

	public function action_doFeatureProcessorShutdown() {
	}

	/**
	 */
	public function init() {
	}

	/**
	 * @return bool
	 */
	protected function readyToRun() {
		return true;
	}

	/**
	 * Override to set what this processor does when it's "run"
	 */
	abstract public function run();

	/**
	 * @param array $aNoticeData
	 * @throws Exception
	 */
	protected function insertAdminNotice( $aNoticeData ) {
		$aAttrs = $aNoticeData[ 'notice_attributes' ];
		$bIsPromo = isset( $aAttrs[ 'type' ] ) && $aAttrs[ 'type' ] == 'promo';
		if ( $bIsPromo && $this->getPromoNoticesCount() > 0 ) {
			return;
		}

		$oNotices = $this->loadAdminNoticesProcessor();
		if ( !$oNotices->isDismissed( $aAttrs[ 'id' ] ) ) {

			$sRenderedNotice = $this->getFeature()->renderAdminNotice( $aNoticeData );
			if ( !empty( $sRenderedNotice ) ) {
				$oNotices->addAdminNotice(
					$sRenderedNotice,
					$aNoticeData[ 'notice_attributes' ][ 'notice_id' ]
				);
				if ( $bIsPromo ) {
					$this->incrementPromoNoticesCount();
				}
			}
		}
	}

	/**
	 * @param       $sOptionKey
	 * @param mixed $mDefault
	 * @return mixed
	 */
	public function getOption( $sOptionKey, $mDefault = false ) {
		return $this->getFeature()->getOpt( $sOptionKey, $mDefault );
	}

	/**
	 * @param string  $sKey
	 * @param mixed   $mValueToTest
	 * @param boolean $bStrict
	 * @return bool
	 */
	public function getIsOption( $sKey, $mValueToTest, $bStrict = false ) {
		$mOptionValue = $this->getOption( $sKey );
		return $bStrict ? $mOptionValue === $mValueToTest : $mOptionValue == $mValueToTest;
	}

	/**
	 * We don't handle locale derivatives (yet)
	 * @return string
	 */
	protected function getGoogleRecaptchaLocale() {
		return str_replace( '_', '-', $this->loadWp()->getLocale() );
	}

	/**
	 * @return mixed
	 */
	public function getPluginDefaultRecipientAddress() {
		return apply_filters( $this->getFeature()->prefix( 'report_email_address' ), $this->loadWp()
																						  ->getSiteAdminEmail() );
	}

	/**
	 * @return ICWP_WPSF_Processor_Email
	 */
	public function getEmailProcessor() {
		return $this->getFeature()->getEmailProcessor();
	}

	/**
	 * @return ICWP_WPSF_FeatureHandler_Base
	 */
	protected function getFeature() {
		return $this->oFeatureOptions;
	}

	/**
	 * @param string $sKey
	 * @return ICWP_WPSF_Processor_Base|null
	 */
	protected function getSubProcessor( $sKey ) {
		$aProcessors = $this->getSubProcessors();
		return isset( $aProcessors[ $sKey ] ) ? $aProcessors[ $sKey ] : null;
	}

	/**
	 * @return ICWP_WPSF_Processor_Base[]
	 */
	protected function getSubProcessors() {
		if ( !isset( $this->aSubProcessors ) ) {
			$this->aSubProcessors = array();
		}
		return $this->aSubProcessors;
	}

	/**
	 * @return ICWP_UserMeta
	 */
	protected function getCurrentUserMeta() {
		return $this->getFeature()->getCurrentUserMeta();
	}

	/**
	 * Will prefix and return any string with the unique plugin prefix.
	 * @param string $sSuffix
	 * @param string $sGlue
	 * @return string
	 */
	protected function prefix( $sSuffix = '', $sGlue = '-' ) {
		return $this->getFeature()->prefix( $sSuffix, $sGlue );
	}

	/**
	 * @return bool|int|string
	 */
	protected function ip() {
		return $this->loadIpService()->getRequestIp();
	}

	/**
	 * @return int
	 */
	protected function time() {
		return $this->loadDP()->time();
	}
}