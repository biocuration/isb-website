<?php

use FernleafSystems\Wordpress\Plugin\Shield\Modules;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\LoginGuard\Lib\AntiBot;
use FernleafSystems\Wordpress\Services\Services;

class ICWP_WPSF_Processor_LoginProtect extends Modules\BaseShield\ShieldProcessor {

	/**
	 */
	public function run() {
		/** @var \ICWP_WPSF_FeatureHandler_LoginProtect $oMod */
		$oMod = $this->getMod();

		// XML-RPC Compatibility
		if ( Services::WpGeneral()->isXmlrpc() && $oMod->isXmlrpcBypass() ) {
			return;
		}

		// So we can allow access to the login pages if IP is whitelisted
		if ( $oMod->isCustomLoginPathEnabled() ) {
			$this->getSubPro( 'rename' )->execute();
		}

		if ( !$oMod->isVisitorWhitelisted() ) {
			( new AntiBot\AntibotSetup() )->setMod( $oMod );
			$oMod->getLoginIntentController()->run();
		}
	}

	/**
	 * Override the original collection to then add plugin statistics to the mix
	 * @param $aData
	 * @return array
	 */
	public function tracking_DataCollect( $aData ) {
		$aData = parent::tracking_DataCollect( $aData );
		$sSlug = $this->getMod()->getSlug();
		$aData[ $sSlug ][ 'options' ][ 'email_can_send_verified_at' ]
			= ( $aData[ $sSlug ][ 'options' ][ 'email_can_send_verified_at' ] > 0 ) ? 1 : 0;
		return $aData;
	}

	/**
	 * @return array
	 */
	protected function getSubProMap() {
		return [
			'rename'    => 'ICWP_WPSF_Processor_LoginProtect_WpLogin',
		];
	}
}