<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin;

use FernleafSystems\Wordpress\Services\Core\VOs\WpPluginVo;
use FernleafSystems\Wordpress\Services\Services;

/**
 * Trait Base
 * @package FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin
 */
trait Base {

	/**
	 * @var string
	 */
	private $sWorkingPluginSlug;

	/**
	 * @var string
	 */
	private $sWorkingPluginVersion;

	/**
	 * @return string
	 */
	public function getWorkingSlug() {
		return $this->sWorkingPluginSlug;
	}

	/**
	 * @return string
	 */
	public function getWorkingVersion() {
		$sVersion = $this->sWorkingPluginVersion;
		if ( empty( $sVersion ) ) {
			$oP = Services::WpPlugins()->getPluginAsVo( $this->getWorkingSlug() );
			if ( $oP instanceof WpPluginVo ) {
				$sVersion = $oP->Version;
			}
		}
		return $sVersion;
	}

	/**
	 * @param string $sSlug
	 * @return $this
	 */
	public function setWorkingSlug( $sSlug ) {
		$this->sWorkingPluginSlug = $sSlug;
		return $this;
	}

	/**
	 * @param string $sV
	 * @return $this
	 */
	public function setWorkingVersion( $sV ) {
		$this->sWorkingPluginVersion = $sV;
		return $this;
	}
}