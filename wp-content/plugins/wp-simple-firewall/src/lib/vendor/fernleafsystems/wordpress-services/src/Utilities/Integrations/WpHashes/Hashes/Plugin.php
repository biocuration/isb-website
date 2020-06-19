<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Hashes;

use FernleafSystems\Wordpress\Services;

/**
 * Class Plugin
 * @package FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Hashes
 */
class Plugin extends PluginThemeBase {

	const TYPE = 'plugin';

	/**
	 * @param Services\Core\VOs\WpPluginVo $oPluginVO
	 * @return array|null
	 */
	public function getHashesFromVO( Services\Core\VOs\WpPluginVo $oPluginVO ) {
		return $this->getHashes( $oPluginVO->slug, $oPluginVO->Version );
	}
}