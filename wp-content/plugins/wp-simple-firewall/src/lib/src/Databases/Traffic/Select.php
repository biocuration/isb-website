<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Databases\Traffic;

use FernleafSystems\Wordpress\Plugin\Shield\Databases\Base;
use FernleafSystems\Wordpress\Plugin\Shield\Utilities\Tool\IpListSort;
use FernleafSystems\Wordpress\Services\Services;

class Select extends Base\Select {

	use BaseTraffic;

	/**
	 * @return string[]
	 */
	public function getDistinctIps() {
		return IpListSort::Sort( array_map(
			function ( $sIpBinary ) {
				return inet_ntop( $sIpBinary );
			},
			$this->getDistinctForColumn( 'ip' )
		) );
	}

	/**
	 * @return string[]
	 */
	public function getDistinctCodes() {
		return $this->getDistinct_FilterAndSort( 'code' );
	}

	/**
	 * @return string[]
	 */
	public function getDistinctUserIds() {
		return $this->getDistinct_FilterAndSort( 'uid' );
	}

	/**
	 * @return string[]
	 */
	public function getDistinctUsernames() {
		$a = array_filter( array_map(
			function ( $nId ) {
				$oUser = Services::WpUsers()->getUserById( $nId );
				return ( $oUser instanceof \WP_User ) ? $oUser->user_login : null;
			},
			$this->getDistinctUserIds()
		) );
		asort( $a );
		return $a;
	}
}