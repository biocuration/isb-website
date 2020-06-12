<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Net;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class VisitorIpDetection
 * @package FernleafSystems\Wordpress\Services\Utilities\Net
 */
class VisitorIpDetection extends BaseIP {

	const DEFAULT_SOURCE = 'REMOTE_ADDR';

	/**
	 * @var bool
	 */
	private $bExcludeHostIps;

	/**
	 * @var string
	 */
	private $sLastSuccessfulSource;

	/**
	 * @var string
	 */
	private $sPreferredSource;

	/**
	 * @var string
	 */
	private $sVisitorIP;

	/**
	 * @return string
	 */
	public function getIP() {
		if ( empty( $this->sVisitorIP ) ) {
			$this->runNormalDetection();
		}
		return $this->sVisitorIP;
	}

	/**
	 */
	private function runNormalDetection() {
		$this->bExcludeHostIps = true;
		list( $sTheSource, $sTheIP ) = $this->findPotentialIpFromSources();
		if ( empty( $sTheSource ) || empty( $sTheIP ) ) {
			$this->bExcludeHostIps = false;
			list( $sTheSource, $sTheIP ) = $this->findPotentialIpFromSources();
		}
		$this->sLastSuccessfulSource = $sTheSource;
		$this->sVisitorIP = $sTheIP;
	}

	/**
	 * @return array
	 */
	private function findPotentialIpFromSources() {

		$aSources = $this->getIpSourceOptions();
		$sPreferred = $this->getPreferredSource();
		if ( in_array( $sPreferred, $aSources ) ) {
			unset( $aSources[ array_search( $sPreferred, $aSources ) ] );
			array_unshift( $aSources, $sPreferred );
		}

		$aTheIPs = [];
		$sTheSource = '';
		foreach ( $aSources as $sMaybeSource ) {
			$aTheIPs = $this->detectAndFilterFromSource( $sMaybeSource );
			if ( !empty( $aTheIPs ) ) {
				$sTheSource = $sMaybeSource;
				break;
			}
		}

		return [ $sTheSource, is_array( $aTheIPs ) ? array_shift( $aTheIPs ) : '' ];
	}

	/**
	 * @param string $sSource
	 * @return string[]
	 */
	protected function detectAndFilterFromSource( $sSource ) {
		return $this->filterIpsByViable( $this->getIpsFromSource( $sSource ) );
	}

	/**
	 * @param string[] $aIps
	 * @return string[]
	 */
	private function filterIpsByViable( $aIps ) {
		return array_values( array_filter(
			$aIps,
			function ( $sIp ) {
				$oIP = Services::IP();
				return ( $oIP->isValidIp_PublicRemote( $sIp )
						 && ( !$this->bExcludeHostIps || !$oIP->checkIp( $sIp, $oIP->getServerPublicIPs() ) )
						 && !Services::ServiceProviders()->isIp_Cloudflare( $sIp )
				);
			}
		) );
	}

	/**
	 * @return string
	 */
	public function getLastSuccessfulSource() {
		return (string)$this->sLastSuccessfulSource;
	}

	/**
	 * @return string
	 */
	public function getPreferredSource() {
		return empty( $this->sPreferredSource ) ? self::DEFAULT_SOURCE : $this->sPreferredSource;
	}

	/**
	 * @param string $sDefaultSource
	 * @return $this
	 */
	public function setPreferredSource( $sDefaultSource ) {
		$this->sPreferredSource = $sDefaultSource;
		return $this;
	}

	/**
	 * @param string[] $aHostIPs
	 * @param bool     $bMerge
	 * @return $this
	 * @deprecated 0.1.49
	 */
	public function setPotentialHostIps( $aHostIPs, $bMerge = true ) {
		return $this;
	}

	/**
	 * @deprecated 0.1.49
	 */
	public function detect() {
		return $this->getIP();
	}

	/**
	 * @return string[]
	 * @deprecated 0.1.49
	 */
	protected function getPotentialHostIps() {
		return [];
	}

	/**
	 * Progressively removes Host IPs from the list so that these don't interfere with detection.
	 * @return string
	 * @deprecated 0.1.49
	 */
	public function alternativeDetect() {
		return $this->getIP();
	}

	/**
	 * @return string
	 * @deprecated 0.1.49
	 */
	private function runNormalDetectionOld() {
		$sSource = '';
		$aIps = $this->detectAndFilterFromSource( $this->getPreferredSource() );

		if ( empty( $aIps ) ) { // Couldn't detect IP from preferred source.

			foreach ( $this->getIpSourceOptions() as $sMaybeSource ) {
				$aIps = $this->detectAndFilterFromSource( $sMaybeSource );
				if ( !empty( $aIps ) ) {
					$sSource = $sMaybeSource;
					break;
				}
			}
		}
		else {
			$sSource = $this->getPreferredSource();
		}

		$this->sLastSuccessfulSource = $sSource;
		return empty( $aIps ) ? '' : array_shift( $aIps );
	}
}