<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Consumers;

use FernleafSystems\Wordpress\Services\Core\VOs\WpPluginVo;

/**
 * Trait PluginConsumer
 * @package FernleafSystems\Wordpress\Services\Utilities\Consumers
 */
trait PluginConsumer {

	/**
	 * @var WpPluginVo
	 */
	private $oWorkingPlugin;

	/**
	 * @return WpPluginVo
	 */
	public function getWorkingPlugin() {
		return $this->oWorkingPlugin;
	}

	/**
	 * @return bool
	 */
	public function hasWorkingPlugin() {
		return $this->oWorkingPlugin instanceof WpPluginVo;
	}

	/**
	 * @param WpPluginVo $oPlugin
	 * @return $this
	 */
	public function setWorkingPlugin( $oPlugin ) {
		$this->oWorkingPlugin = $oPlugin;
		return $this;
	}
}