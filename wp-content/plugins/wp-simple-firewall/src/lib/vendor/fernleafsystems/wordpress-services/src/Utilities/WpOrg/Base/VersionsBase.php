<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Base;

use FernleafSystems\Wordpress\Services;

/**
 * Class VersionsBase
 * @package FernleafSystems\Wordpress\Services\Utilities\WpOrg\Base
 */
abstract class VersionsBase {

	/**
	 * @var string[]
	 */
	private $aWpVersions;

	/**
	 * @return string[]
	 */
	public function all() {
		if ( empty( $this->aWpVersions ) ) {
			$this->aWpVersions = $this->downloadVersions();
			usort( $this->aWpVersions, 'version_compare' );
		}
		return $this->aWpVersions;
	}

	/**
	 * @param string $sVersionBranch - leave empty to use the current WP Version
	 * @return string
	 * @throws \Exception
	 */
	public function getLatestVersionForBranch( $sVersionBranch = null ) {
		if ( empty( $sVersionBranch ) ) {
			$sVersionBranch = Services\Services::WpGeneral()->getVersion();
		}
		$aParts = explode( '.', $sVersionBranch );
		if ( count( $aParts ) < 2 ) {
			throw new \Exception( sprintf( 'Invalid version "%s" provided.', $sVersionBranch ) );
		}

		$sThisBranch = $aParts[ 0 ].'.'.$aParts[ 1 ];

		$aPossible = array_filter(
			$this->all(),
			function ( $sVersion ) use ( $sThisBranch ) {
				return strpos( $sVersion, $sThisBranch ) === 0;
			}
		);
		return end( $aPossible );
	}

	/**
	 * @return string
	 */
	public function latest() {
		$aVs = $this->all();
		return end( $aVs );
	}

	abstract protected function downloadVersions();
}