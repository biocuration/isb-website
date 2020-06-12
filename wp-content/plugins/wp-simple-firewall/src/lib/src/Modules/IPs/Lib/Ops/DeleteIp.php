<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\IPs\Lib\Ops;

use FernleafSystems\Wordpress\Plugin\Shield;
use FernleafSystems\Wordpress\Plugin\Shield\Databases;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\IPs;

/**
 * Class DeleteIp
 * @package FernleafSystems\Wordpress\Plugin\Shield\Modules\IPs\Lib\Ops
 */
class DeleteIp {

	use Databases\Base\HandlerConsumer;
	use IPs\Components\IpAddressConsumer;

	/**
	 * @var string
	 */
	private $sIpAddress;

	/**
	 * @return bool
	 */
	public function fromBlacklist() {
		return $this->getDeleter()
					->filterByBlacklist()
					->query();
	}

	/**
	 * @return bool
	 */
	public function fromWhiteList() {
		return $this->getDeleter()
					->filterByWhitelist()
					->query();
	}

	/**
	 * @return Databases\IPs\Delete
	 */
	private function getDeleter() {
		/** @var Databases\IPs\Delete $oDel */
		$oDel = $this->getDbHandler()->getQueryDeleter();
		return $oDel->filterByIp( $this->getIP() )
					->setLimit( 1 );
	}

	/**
	 * @return string
	 */
	public function getIP() {
		return $this->sIpAddress;
	}

	/**
	 * @param string $sIP
	 * @return $this
	 */
	public function setIP( $sIP ) {
		$this->sIpAddress = $sIP;
		return $this;
	}
}