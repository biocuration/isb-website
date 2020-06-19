<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Scans\Base\Files;

use FernleafSystems\Wordpress\Plugin\Shield;

/**
 * Class BaseFileScanner
 * @package FernleafSystems\Wordpress\Plugin\Shield\Scans\Base\Files
 */
abstract class BaseFileScanner {

	use Shield\Modules\ModConsumer;
	use Shield\Scans\Common\ScanActionConsumer;

	/**
	 * @param string $sFullPath
	 * @return Shield\Scans\Base\BaseResultItem|null
	 */
	abstract public function scan( $sFullPath );
}