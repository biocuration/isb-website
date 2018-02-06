<?php

if ( class_exists( 'ICWP_WPSF_Processor_Plugin_Tracking', false ) ) {
	return;
}

class ICWP_WPSF_Processor_Plugin_Tracking extends ICWP_WPSF_Processor_BasePlugin {

	public function run() {
		/** @var ICWP_WPSF_FeatureHandler_Plugin $oFO */
		$oFO = $this->getFeature();

		if ( $oFO->isTrackingEnabled() ) {
			$this->createTrackingCollectionCron();
		}
		add_action( $oFO->prefix( 'delete_plugin' ), array( $this, 'deleteCron' ) );
	}

	/**
	 * @see autoAddToAdminNotices()
	 * @param array $aNoticeAttributes
	 */
	protected function addNotice_allow_tracking( $aNoticeAttributes ) {
		/** @var ICWP_WPSF_FeatureHandler_Plugin $oFO */
		$oFO = $this->getFeature();

		if ( $this->getIfShowAdminNotices() && !$oFO->isTrackingPermissionSet() ) {
			$oCon = $this->getController();
			$aRenderData = array(
				'notice_attributes' => $aNoticeAttributes,
				'strings'           => array(
					'title'           => sprintf( _wpsf__( "Make %s even better by sharing usage info?" ), $oCon->getHumanName() ),
					'want_to_track'   => sprintf( _wpsf__( "We're hoping to understand how %s is configured and used." ), $oCon->getHumanName() ),
					'what_we_collect' => _wpsf__( "We'd like to understand how effective it is on a global scale." ),
					'data_anon'       => _wpsf__( 'The data sent is always completely anonymous and we can never track you or your site.' ),
					'can_turn_off'    => _wpsf__( 'It can be turned-off at any time within the plugin options.' ),
					'click_to_see'    => _wpsf__( 'Click to see the RAW data that would be sent' ),
					'learn_more'      => _wpsf__( 'Learn More.' ),
					'site_url'        => 'translate.icontrolwp.com',
					'yes'             => _wpsf__( 'Absolutely' )
				),
				'hrefs'             => array(
					'learn_more'       => 'http://translate.icontrolwp.com',
					'link_to_see'      => $oFO->getLinkToTrackingDataDump(),
					'link_to_moreinfo' => 'http://icwp.io/shieldtrackinginfo',

				)
			);
			$this->insertAdminNotice( $aRenderData );
		}
	}

	/**
	 */
	public function sendTrackingData() {
		/** @var ICWP_WPSF_FeatureHandler_Plugin $oFO */
		$oFO = $this->getFeature();
		if ( !$oFO->isTrackingEnabled() || !$oFO->readyToSendTrackingData() ) {
			return false;
		}

		$aData = $this->collectTrackingData();
		if ( empty( $aData ) || !is_array( $aData ) ) {
			return false;
		}

		$mResult = $this->loadFS()->requestUrl(
			$oFO->getDefinition( 'tracking_post_url' ),
			array(
				'method'      => 'POST',
				'timeout'     => 20,
				'redirection' => 5,
				'httpversion' => '1.1',
				'blocking'    => true,
				'body'        => array( 'tracking_data' => $aData ),
				'user-agent'  => 'SHIELD/'.$this->getController()->getVersion().';'
			),
			true
		);
		$oFO->setTrackingLastSentAt();
		return $mResult;
	}

	/**
	 * @return array
	 */
	public function collectTrackingData() {
		$aData = apply_filters(
			$this->getFeature()->prefix( 'collect_tracking_data' ),
			$this->getBaseTrackingData()
		);
		return is_array( $aData ) ? $aData : array();
	}

	/**
	 * @return array
	 */
	protected function getBaseTrackingData() {
		$oDP = $this->loadDataProcessor();
		$oWP = $this->loadWp();
		$oWpPlugins = $this->loadWpPlugins();
		return array(
			'env' => array(
				'options' => array(
					'php'             => $oDP->getPhpVersionCleaned(),
					'wordpress'       => $oWP->getVersion(),
					'slug'            => $this->getController()->getPluginSlug(),
					'version'         => $this->getController()->getVersion(),
					'is_wpms'         => $oWP->isMultisite() ? 1 : 0,
					'ssl'             => ( $oDP->FetchServer( 'HTTPS' ) == 'on' ) ? 1 : 0,
					'locale'          => get_locale(),
					'plugins_total'   => count( $oWpPlugins->getPlugins() ),
					'plugins_active'  => count( $oWpPlugins->getActivePlugins() ),
					'plugins_updates' => count( $oWpPlugins->getUpdates() )
				)
			)
		);
	}

	/**
	 * @throws Exception
	 */
	protected function createTrackingCollectionCron() {
		/** @var ICWP_WPSF_FeatureHandler_Plugin $oFO */
		$oFO = $this->getFeature();
		$sFullHookName = $this->getCronName();
		$this->loadWpCronProcessor()
			 ->setNextRun( strtotime( 'tomorrow 3am' ) - get_option( 'gmt_offset' )*HOUR_IN_SECONDS + rand( 0, 1800 ) )
			 ->setRecurrence( 'daily' )
			 ->createCronJob( $sFullHookName, array( $this, 'sendTrackingData' ) );
	}

	public function deleteCron() {
		$this->loadWpCronProcessor()->deleteCronJob( $this->getCronName() );
	}

	/**
	 * @return string
	 */
	public function getCronName() {
		$oFO = $this->getFeature();
		return $oFO->prefix( $oFO->getDefinition( 'tracking_cron_handle' ) );
	}
}