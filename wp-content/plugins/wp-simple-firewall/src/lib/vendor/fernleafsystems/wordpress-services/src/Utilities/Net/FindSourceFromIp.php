<?php

namespace FernleafSystems\Wordpress\Services\Utilities\Net;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class FindSourceFromIp
 * @package FernleafSystems\Wordpress\Services\Utilities\Net
 */
class FindSourceFromIp extends BaseIP {

	/**
	 * @param string $sIP
	 * @return string|null
	 */
	public function run( $sIP ) {
		$sTheSource = null;
		foreach ( $this->getIpSourceOptions() as $sSource ) {
			try {
				if ( Services::IP()->checkIp( $sIP, $this->getIpsFromSource( $sSource ) ) ) {
					$sTheSource = $sSource;
					break;
				}
			}
			catch ( \Exception $oE ) {
			}
		}
		return $sTheSource;
	}
}