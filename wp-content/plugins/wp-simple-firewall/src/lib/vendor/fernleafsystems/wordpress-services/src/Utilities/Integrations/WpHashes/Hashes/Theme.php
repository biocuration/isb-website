<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Hashes;

use FernleafSystems\Wordpress\Services;

/**
 * Class Theme
 * @package FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Hashes
 */
class Theme extends PluginThemeBase {

	const TYPE = 'theme';

	/**
	 * @param Services\Core\VOs\WpThemeVo $oVO
	 * @return array|null
	 */
	public function getHashesFromVO( Services\Core\VOs\WpThemeVo $oVO ) {
		return $this->getHashes( $oVO->stylesheet, $oVO->version );
	}
}