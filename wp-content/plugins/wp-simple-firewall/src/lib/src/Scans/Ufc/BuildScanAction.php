<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Scans\Ufc;

use FernleafSystems\Wordpress\Plugin\Shield;

/**
 * Class BuildScanAction
 * @package FernleafSystems\Wordpress\Plugin\Shield\Scans\Ufc
 */
class BuildScanAction extends Shield\Scans\Base\BaseBuildScanAction {

	protected function buildItems() {
		/** @var ScanActionVO $oAction */
		$oAction = $this->getScanActionVO();
		$oAction->items = ( new Shield\Scans\Ufc\BuildFileMap() )
			->setScanActionVO( $oAction )
			->build();
	}

	protected function setCustomFields() {
		/** @var ScanActionVO $oAction */
		$oAction = $this->getScanActionVO();
		/** @var Shield\Modules\HackGuard\Options $oOpts */
		$oOpts = $this->getOptions();

		$aExcls = $oOpts->getOpt( 'ufc_exclusions', [] );
		$oAction->exclusions = is_array( $aExcls ) ? $aExcls : [];
		$oAction->scan_dirs = $oOpts->getUfcScanDirectories();
	}
}