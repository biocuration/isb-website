<?php

use FernleafSystems\Wordpress\Plugin\Shield;
use FernleafSystems\Wordpress\Plugin\Shield\Modules;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\Plugin;
use FernleafSystems\Wordpress\Services\Services;

class ICWP_WPSF_Processor_Plugin extends Modules\BaseShield\ShieldProcessor {

	public function run() {
		parent::run();
		/** @var \ICWP_WPSF_FeatureHandler_Plugin $oMod */
		$oMod = $this->getMod();
		/** @var Plugin\Options $oOpts */
		$oOpts = $this->getOptions();

		$this->removePluginConflicts();

		$oMod->getPluginBadgeCon()->run();

		if ( $oOpts->isTrackingEnabled() || !$oOpts->isTrackingPermissionSet() ) {
			$this->getSubProTracking()->execute();
		}

		if ( $oOpts->isImportExportPermitted() ) {
			$oMod->getImpExpController()->run();
		}

		$oCon = $this->getCon();
		switch ( $oCon->getShieldAction() ) {
			case 'dump_tracking_data':
				add_action( 'wp_loaded', [ $this, 'dumpTrackingData' ] );
				break;
			default:
				break;
		}

		add_action( 'admin_footer', [ $this, 'printAdminFooterItems' ], 100, 0 );

		add_filter( $oCon->prefix( 'delete_on_deactivate' ), function ( $bDelete ) use ( $oOpts ) {
			return $bDelete || $oOpts->isOpt( 'delete_on_deactivate', 'Y' );
		} );

		add_action( $oCon->prefix( 'dashboard_widget_content' ),
			[ $this, 'printDashboardWidget' ],
			100
		);
	}

	public function printDashboardWidget() {
		$oCon = $this->getCon();
		/** @var Plugin\Options $oOpts */
		$oOpts = $this->getOptions();
		$aLabels = $oCon->getLabels();

		echo $this->getMod()->renderTemplate(
			'snippets/widget_dashboard_plugin.php',
			[
				'install_days' => sprintf( __( 'Days Installed: %s', 'wp-simple-firewall' ), $oOpts->getInstallationDays() ),
				'footer'       => sprintf( __( '%s is provided by %s', 'wp-simple-firewall' ), $oCon->getHumanName(),
					sprintf( '<a href="%s" target="_blank">%s</a>', $aLabels[ 'AuthorURI' ], $aLabels[ 'Author' ] )
				),
				'ip_address'   => sprintf( __( 'Your IP address is: %s', 'wp-simple-firewall' ), Services::IP()
																										 ->getRequestIp() )
			]
		);
	}

	/**
	 * @return \ICWP_WPSF_Processor_Plugin_Tracking
	 */
	protected function getSubProTracking() {
		return $this->getSubPro( 'tracking' );
	}

	/**
	 * @return array
	 */
	protected function getSubProMap() {
		return [
			'tracking' => 'ICWP_WPSF_Processor_Plugin_Tracking',
		];
	}

	public function printAdminFooterItems() {
		$this->printPluginDeactivateSurvey();
		$this->printToastTemplate();
	}

	/**
	 * Sets this plugin to be the first loaded of all the plugins.
	 */
	private function printToastTemplate() {
		if ( $this->getCon()->isModulePage() ) {
			$aRenderData = [
				'strings'     => [
					'title' => $this->getCon()->getHumanName(),
				],
				'js_snippets' => []
			];
			echo $this->getMod()->renderTemplate(
				'snippets/toaster.twig', $aRenderData, true
			);
		}
	}

	private function printPluginDeactivateSurvey() {
		if ( Services::WpPost()->isCurrentPage( 'plugins.php' ) ) {

			$aOpts = [
				'reason_confusing'   => "It's too confusing",
				'reason_expected'    => "It's not what I expected",
				'reason_accident'    => "I downloaded it accidentally",
				'reason_alternative' => "I'm already using an alternative",
				'reason_trust'       => "I don't trust the developer :(",
				'reason_not_work'    => "It doesn't work",
				'reason_errors'      => "I'm getting errors",
			];

			$aData = [
				'strings'     => [
					'editing_restricted' => __( 'Editing this option is currently restricted.', 'wp-simple-firewall' ),
				],
				'inputs'      => [
					'checkboxes' => Services::DataManipulation()->shuffleArray( $aOpts )
				],
				'js_snippets' => []
			];
			echo $this->getMod()->renderTemplate(
				'snippets/plugin-deactivate-survey.php', $aData
			);
		}
	}

	public function dumpTrackingData() {
		if ( $this->getCon()->isPluginAdmin() ) {
			echo sprintf( '<pre><code>%s</code></pre>',
				print_r( $this->getSubProTracking()->collectTrackingData(), true ) );
			die();
		}
	}

	public function runDailyCron() {
		$this->getCon()->fireEvent( 'test_cron_run' );

		/** @var Plugin\Options $oOpts */
		$oOpts = $this->getOptions();
		if ( $oOpts->isImportExportPermitted() ) {
			( new Plugin\Lib\ImportExport\Import() )
				->setMod( $this->getMod() )
				->fromSite( $oOpts->getImportExportMasterImportUrl() );
		}

		( new Shield\Utilities\Options\CleanStorage() )
			->setCon( $this->getCon() )
			->run();
	}

	/**
	 * Lets you remove certain plugin conflicts that might interfere with this plugin
	 */
	protected function removePluginConflicts() {
		if ( class_exists( 'AIO_WP_Security' ) && isset( $GLOBALS[ 'aio_wp_security' ] ) ) {
			remove_action( 'init', [ $GLOBALS[ 'aio_wp_security' ], 'wp_security_plugin_init' ], 0 );
		}
	}

	/**
	 * Override the original collection to then add plugin statistics to the mix
	 * @param array $aData
	 * @return array
	 */
	public function tracking_DataCollect( $aData ) {
		$aData = parent::tracking_DataCollect( $aData );
		/** @var \ICWP_WPSF_FeatureHandler_Plugin $oMod */
		$oMod = $this->getMod();
		$sSlug = $oMod->getSlug();
		if ( empty( $aData[ $sSlug ][ 'options' ][ 'unique_installation_id' ] ) ) {
			$aData[ $sSlug ][ 'options' ][ 'unique_installation_id' ] = $oMod->getPluginInstallationId();
			$aData[ $sSlug ][ 'options' ][ 'new_unique_installation_id' ] = $this->getCon()->getSiteInstallationId();
		}
		return $aData;
	}
}