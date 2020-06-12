<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Theme;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Trait Base
 * @package FernleafSystems\Wordpress\Services\Utilities\WpOrg\Theme
 */
trait Base {

	/**
	 * @var string
	 */
	private $sWorkingSlug;

	/**
	 * @var string
	 */
	private $sWorkingVersion;

	/**
	 * @return string
	 */
	public function getWorkingSlug() {
		return $this->sWorkingSlug;
	}

	/**
	 * @return string
	 */
	public function getWorkingVersion() {
		$sVersion = $this->sWorkingVersion;
		if ( empty( $sVersion ) ) {
			$oT = Services::WpThemes()->getTheme( $this->getWorkingSlug() );
			if ( $oT instanceof \WP_Theme ) {
				$sVersion = $oT->get( 'Version' );
			}
		}
		return $sVersion;
	}

	/**
	 * @param string $sSlug
	 * @return $this
	 */
	public function setWorkingSlug( $sSlug ) {
		$this->sWorkingSlug = $sSlug;
		return $this;
	}

	/**
	 * @param string $sV
	 * @return $this
	 */
	public function setWorkingVersion( $sV ) {
		$this->sWorkingVersion = $sV;
		return $this;
	}
}