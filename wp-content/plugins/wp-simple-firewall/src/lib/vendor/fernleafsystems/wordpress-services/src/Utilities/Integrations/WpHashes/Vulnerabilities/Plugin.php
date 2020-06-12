<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Vulnerabilities;

use FernleafSystems\Wordpress\Services;

/**
 * Class Plugin
 * @package FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Vulnerabilities
 */
class Plugin extends BasePluginTheme {

	const ASSET_TYPE = 'plugin';

	/**
	 * @param Services\Core\VOs\WpPluginVo $oPluginVO
	 * @return array[]|null
	 */
	public function getFromVO( Services\Core\VOs\WpPluginVo $oPluginVO ) {
		return $this->getVulnerabilities( $oPluginVO->slug, $oPluginVO->Version );
	}
}