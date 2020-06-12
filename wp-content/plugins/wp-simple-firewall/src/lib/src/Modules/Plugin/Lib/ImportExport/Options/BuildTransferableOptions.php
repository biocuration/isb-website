<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\Plugin\Lib\ImportExport\Options;

use FernleafSystems\Wordpress\Plugin\Shield\Modules\ModConsumer;

class BuildTransferableOptions {

	use ModConsumer;

	/**
	 * @return mixed[]
	 */
	public function build() {
		$oOpts = $this->getOptions();
		return array_merge(
			array_fill_keys( $oOpts->getOptionsKeys(), false ),
			array_fill_keys( array_keys( $oOpts->getTransferableOptions() ), 'Y' ),
			array_fill_keys( $oOpts->getXferExcluded(), 'N' )
		);
	}
}
