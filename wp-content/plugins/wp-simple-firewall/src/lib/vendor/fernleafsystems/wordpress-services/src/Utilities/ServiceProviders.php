<?php

namespace FernleafSystems\Wordpress\Services\Utilities;

use FernleafSystems\Wordpress\Services\Services;
use FernleafSystems\Wordpress\Services\Utilities\Integrations\WpHashes\Services\IPs;
use FernleafSystems\Wordpress\Services\Utilities\Options\Transient;

/**
 * Class ServiceProviders
 * @package FernleafSystems\Wordpress\Services\Utilities
 */
class ServiceProviders {

	/**
	 * @return string[]
	 */
	public function getAllCrawlerUseragents() {
		return [
			'Applebot/',
			'baidu',
			'bingbot',
			'Googlebot',
			'APIs-Google',
			'AdsBot-Google',
			'Mediapartners-Google',
			'SemrushBot',
			'yandex.com/bots',
			'yahoo!'
		];
	}

	/**
	 * @return string[][][]|null
	 */
	protected function getAllServiceIPs() {
		$aIps = Transient::Get( 'serviceips_all' );
		if ( empty( $aIps ) ) {
			$aIps = ( new IPs() )->getIPs();
			$aIps = Transient::Set( 'serviceips_all',$aIps, WEEK_IN_SECONDS );
		}
		return $aIps;
	}

	/**
	 * @return string[][]
	 */
	public function getIps_CloudFlare() {
		return $this->getIpsForSlug( 'cloudflare' );
	}

	/**
	 * @return string[]
	 */
	public function getIps_CloudFlareV4() {
		return $this->getIps_CloudFlare()[ 4 ];
	}

	/**
	 * @return string[]
	 */
	public function getIps_CloudFlareV6() {
		return $this->getIps_CloudFlare()[ 6 ];
	}

	/**
	 * @param bool $bFlat
	 * @return string[]|string[][]
	 */
	public function getIps_DuckDuckGo( $bFlat = false ) {
		return $this->getIpsForSlug( 'duckduckgo', $bFlat );
	}

	/**
	 * @param bool $bFlat
	 * @return string[][]|string[]
	 */
	public function getIps_iControlWP( $bFlat = false ) {
		return $this->getIpsForSlug( 'icontrolwp', $bFlat );
	}

	/**
	 * @param bool $bFlat
	 * @return string[][]|string[]
	 */
	public function getIps_ManageWp( $bFlat = false ) {
		return $this->getIpsForSlug( 'managewp', $bFlat );
	}

	/**
	 * @param bool $bFlat
	 * @return string[][]|string[]
	 */
	public function getIps_NodePing( $bFlat = false ) {
		return $this->getIpsForSlug( 'nodeping', $bFlat );
	}

	/**
	 * @param bool $bFlat
	 * @return string[][]|string[]
	 */
	public function getIps_Pingdom( $bFlat = false ) {
		return $this->getIpsForSlug( 'pingdom', $bFlat );
	}

	/**
	 * @param bool $bFlat
	 * @return string[]|\string[][]
	 */
	public function getIps_Statuscake( $bFlat = false ) {
		return $this->getIpsForSlug( 'statuscake', $bFlat );
	}

	/**
	 * @param bool $bFlat
	 * @return string[][]
	 */
	public function getIps_Sucuri( $bFlat = false ) {
		return $this->getIpsForSlug( 'sucuri', $bFlat );
	}

	/**
	 * @param bool $bFlat - false for segregated IPv4 and IPv6
	 * @return string[][]|string[]
	 */
	public function getIps_UptimeRobot( $bFlat = false ) {
		return $this->getIpsForSlug( 'uptimerobot', $bFlat );
	}

	/**
	 * @param string $sSlug
	 * @param bool   $bFlat
	 * @return string[][]|string[]
	 */
	public function getIpsForSlug( $sSlug, $bFlat = false ) {
		$aAll = $this->getAllServiceIPs();
		$aIPs = empty( $aAll[ $sSlug ] ) ? [ 4 => [], 6 => [] ] : $aAll[ $sSlug ];
		return $bFlat ? array_merge( $aIPs[ 4 ], $aIPs[ 6 ] ) : $aIPs;
	}

	/**
	 * @param string $sIp
	 * @param string $sUserAgent
	 * @return bool
	 */
	public function isIp_AppleBot( $sIp, $sUserAgent ) {
		$oWp = Services::WpGeneral();

		$sStoreKey = $this->getPrefixedStoreKey( 'serviceips_applebot' );
		$aIps = $oWp->getTransient( $sStoreKey );
		if ( !is_array( $aIps ) ) {
			$aIps = [];
		}

		if ( !in_array( $sIp, $aIps ) && $this->verifyIp_AppleBot( $sIp, $sUserAgent ) ) {
			$aIps[] = $sIp;
			$oWp->setTransient( $sStoreKey, $aIps, WEEK_IN_SECONDS*4 );
		}

		return in_array( $sIp, $aIps );
	}

	/**
	 * @param string $sIp
	 * @param string $sUserAgent
	 * @return bool
	 */
	public function isIp_BaiduBot( $sIp, $sUserAgent ) {
		$oWp = Services::WpGeneral();

		$sStoreKey = $this->getPrefixedStoreKey( 'serviceips_baidubot' );
		$aIps = $oWp->getTransient( $sStoreKey );
		if ( !is_array( $aIps ) ) {
			$aIps = [];
		}

		if ( !in_array( $sIp, $aIps ) && $this->verifyIp_BaiduBot( $sIp, $sUserAgent ) ) {
			$aIps[] = $sIp;
			$oWp->setTransient( $sStoreKey, $aIps, WEEK_IN_SECONDS*4 );
		}

		return in_array( $sIp, $aIps );
	}

	/**
	 * @param string $sIp
	 * @param string $sUserAgent
	 * @return bool
	 */
	public function isIp_BingBot( $sIp, $sUserAgent ) {
		$oWp = Services::WpGeneral();

		$sStoreKey = $this->getPrefixedStoreKey( 'serviceips_bingbot' );
		$aIps = $oWp->getTransient( $sStoreKey );
		if ( !is_array( $aIps ) ) {
			$aIps = [];
		}

		if ( !in_array( $sIp, $aIps ) && $this->verifyIp_BingBot( $sIp, $sUserAgent ) ) {
			$aIps[] = $sIp;
			$oWp->setTransient( $sStoreKey, $aIps, WEEK_IN_SECONDS*4 );
		}

		return in_array( $sIp, $aIps );
	}

	/**
	 * @param string $sIp
	 * @return bool
	 */
	public function isIp_Cloudflare( $sIp ) {
		return $this->isIpInCollection( $sIp, $this->getIpsForSlug( 'cloudflare' ) );
	}

	/**
	 * https://duckduckgo.com/duckduckbot
	 * @param string $sIp
	 * @param string $sUserAgent
	 * @return bool
	 */
	public function isIp_DuckDuckGoBot( $sIp, $sUserAgent ) {
		return ( is_null( $sUserAgent ) || stripos( $sUserAgent, 'DuckDuckBot' ) !== false )
			   && $this->isIpInCollection( $sIp, $this->getIpsForSlug( 'duckduckgo' ) );
	}

	/**
	 * @param string     $sCheckIP
	 * @param string[][] $aSet
	 * @return bool
	 */
	public function isIpInCollection( $sCheckIP, $aSet ) {
		$bExists = false;
		try {
			$oIpUtil = Services::IP();
			$nVer = $oIpUtil->getIpVersion( $sCheckIP );
			$bExists = $nVer !== false && $oIpUtil->checkIp( $sCheckIP, $aSet[ $nVer ] );
		}
		catch ( \Exception $oE ) {
		}
		return $bExists;
	}

	/**
	 * @param string $sIp
	 * @param string $sAgent
	 * @return bool
	 */
	public function isIp_iControlWP( $sIp, $sAgent = null ) { //TODO: Agent
		$bIsBot = false;
		if ( is_null( $sAgent ) || stripos( $sAgent, 'iControlWPApp' ) !== false ) {
			$bIsBot = $this->isIpInCollection( $sIp, $this->getIpsForSlug( 'icontrolwp' ) );
		}
		return $bIsBot;
	}

	/**
	 * https://support.google.com/webmasters/answer/80553?hl=en
	 * @param string $sIp
	 * @param string $sUserAgent
	 * @return bool
	 */
	public function isIp_GoogleBot( $sIp, $sUserAgent ) {
		$oWp = Services::WpGeneral();

		$sStoreKey = $this->getPrefixedStoreKey( 'serviceips_googlebot' );
		$aIps = $oWp->getTransient( $sStoreKey );
		if ( !is_array( $aIps ) ) {
			$aIps = [];
		}

		if ( !in_array( $sIp, $aIps ) && $this->verifyIp_GoogleBot( $sIp, $sUserAgent ) ) {
			$aIps[] = $sIp;
			$oWp->setTransient( $sStoreKey, $aIps, WEEK_IN_SECONDS*4 );
		}

		return in_array( $sIp, $aIps );
	}

	/**
	 * @param string $sIp
	 * @param string $sAgent
	 * @return bool
	 */
	public function isIp_Pingdom( $sIp, $sAgent ) {
		return ( stripos( $sAgent, 'pingdom.com' ) !== false )
			   && $this->isIpInCollection( $sIp, $this->getIpsForSlug( 'pingdom' ) );
	}

	/**
	 * @param string $sIp
	 * @param string $sAgent
	 * @return bool
	 */
	public function isIp_Stripe( $sIp, $sAgent ) {
		return ( stripos( $sAgent, 'Stripe/' ) !== false )
			   && $this->isIpInCollection( $sIp, $this->getIpsForSlug( 'stripe' ) );
	}

	/**
	 * @param string $sIp
	 * @param string $sUserAgent
	 * @return bool
	 */
	public function isIp_SemRush( $sIp, $sUserAgent ) {
		$oWp = Services::WpGeneral();

		$sStoreKey = $this->getPrefixedStoreKey( 'serviceips_semrush' );
		$aIps = $oWp->getTransient( $sStoreKey );
		if ( !is_array( $aIps ) ) {
			$aIps = [];
		}

		if ( !in_array( $sIp, $aIps ) && $this->verifyIp_SemRush( $sIp, $sUserAgent ) ) {
			$aIps[] = $sIp;
			$oWp->setTransient( $sStoreKey, $aIps, WEEK_IN_SECONDS*4 );
		}

		return in_array( $sIp, $aIps );
	}

	/**
	 * @param string $sIp
	 * @param string $sAgent
	 * @return bool
	 */
	public function isIp_Statuscake( $sIp, $sAgent ) {
		return ( stripos( $sAgent, 'StatusCake' ) !== false )
			   && $this->isIpInCollection( $sIp, $this->getIpsForSlug( 'statuscake' ) );
	}

	/**
	 * @param string $sIp
	 * @param string $sAgent
	 * @return bool
	 */
	public function isIp_UptimeRobot( $sIp, $sAgent ) {
		return ( stripos( $sAgent, 'UptimeRobot' ) !== false )
			   && $this->isIpInCollection( $sIp, $this->getIpsForSlug( 'uptimerobot' ) );
	}

	/**
	 * https://yandex.com/support/webmaster/robot-workings/check-yandex-robots.html
	 * @param string $sIp
	 * @param string $sUserAgent
	 * @return bool
	 */
	public function isIp_YandexBot( $sIp, $sUserAgent ) {
		$oWp = Services::WpGeneral();

		$sStoreKey = $this->getPrefixedStoreKey( 'serviceips_yandexbot' );
		$aIps = $oWp->getTransient( $sStoreKey );
		if ( !is_array( $aIps ) ) {
			$aIps = [];
		}

		if ( !in_array( $sIp, $aIps ) && $this->verifyIp_YandexBot( $sIp, $sUserAgent ) ) {
			$aIps[] = $sIp;
			$oWp->setTransient( $sStoreKey, $aIps, WEEK_IN_SECONDS*4 );
		}

		return in_array( $sIp, $aIps );
	}

	/**
	 * https://yandex.com/support/webmaster/robot-workings/check-yandex-robots.html
	 * @param string $sIp
	 * @param string $sUserAgent
	 * @return bool
	 */
	public function isIp_YahooBot( $sIp, $sUserAgent ) {
		$oWp = Services::WpGeneral();

		$sStoreKey = $this->getPrefixedStoreKey( 'serviceips_yahoobot' );
		$aIps = $oWp->getTransient( $sStoreKey );
		if ( !is_array( $aIps ) ) {
			$aIps = [];
		}

		if ( !in_array( $sIp, $aIps ) && $this->verifyIp_YahooBot( $sIp, $sUserAgent ) ) {
			$aIps[] = $sIp;
			$oWp->setTransient( $sStoreKey, $aIps, WEEK_IN_SECONDS*4 );
		}

		return in_array( $sIp, $aIps );
	}

	/**
	 * https://support.apple.com/en-gb/HT204683
	 * https://discussions.apple.com/thread/7090135
	 * Apple IPs start with '17.'
	 * @param string $sIp
	 * @param string $sUserAgent
	 * @return bool
	 */
	private function verifyIp_AppleBot( $sIp, $sUserAgent = '' ) {
		return ( Services::IP()->getIpVersion( $sIp ) != 4 || strpos( $sIp, '17.' ) === 0 )
			   && $this->isIpOfBot( [ 'Applebot/' ], '#.*\.applebot.apple.com\.?$#i', $sIp, $sUserAgent );
	}

	/**
	 * @param string $sIp
	 * @param string $sUserAgent
	 * @return bool
	 */
	private function verifyIp_BaiduBot( $sIp, $sUserAgent = '' ) {
		return $this->isIpOfBot( [ 'baidu' ], '#.*\.crawl\.baidu\.(com|jp)\.?$#i', $sIp, $sUserAgent );
	}

	/**
	 * @param string $sIp
	 * @param string $sUserAgent
	 * @return bool
	 */
	private function verifyIp_BingBot( $sIp, $sUserAgent = '' ) {
		return $this->isIpOfBot( [ 'bingbot' ], '#.*\.search\.msn\.com\.?$#i', $sIp, $sUserAgent );
	}

	/**
	 * @param string $sIp
	 * @param string $sUserAgent
	 * @return bool
	 */
	private function verifyIp_GoogleBot( $sIp, $sUserAgent = '' ) {
		return $this->isIpOfBot(
			[ 'Googlebot', 'APIs-Google', 'AdsBot-Google', 'Mediapartners-Google' ],
			'#.*\.google(bot)?\.com\.?$#i', $sIp, $sUserAgent
		);
	}

	/**
	 * @param string $sIp
	 * @param string $sUserAgent
	 * @return bool
	 */
	private function verifyIp_SemRush( $sIp, $sUserAgent = '' ) {
		return $this->isIpOfBot( [ 'SemrushBot' ], '#.*\.bot\.semrush\.com\.?$#i', $sIp, $sUserAgent );
	}

	/**
	 * @param string $sIp
	 * @param string $sUserAgent
	 * @return bool
	 */
	private function verifyIp_YandexBot( $sIp, $sUserAgent = '' ) {
		return $this->isIpOfBot( [ 'yandex.com/bots' ], '#.*\.yandex?\.(com|ru|net)\.?$#i', $sIp, $sUserAgent );
	}

	/**
	 * @param string $sIp
	 * @param string $sUserAgent
	 * @return bool
	 */
	private function verifyIp_YahooBot( $sIp, $sUserAgent = '' ) {
		return $this->isIpOfBot( [ 'yahoo!' ], '#.*\.crawl\.yahoo\.net\.?$#i', $sIp, $sUserAgent );
	}

	/**
	 * @param string $sIp
	 * @return bool
	 */
	private function verifyIp_Sucuri( $sIp ) {
		$sHost = @gethostbyaddr( $sIp ); // returns the ip on failure
		return !empty( $sHost ) && ( $sHost != $sIp )
			   && preg_match( '#.*\.sucuri\.net\.?$#i', $sHost )
			   && gethostbyname( $sHost ) === $sIp;
	}

	/**
	 * Will test useragent, then attempt to resolve to hostname and back again
	 * https://www.elephate.com/detect-verify-crawlers/
	 * @param array  $aBotUserAgents
	 * @param string $sBotHostPattern
	 * @param string $sReqIp
	 * @param string $sReqUserAgent
	 * @return bool
	 */
	private function isIpOfBot( $aBotUserAgents, $sBotHostPattern, $sReqIp, $sReqUserAgent = '' ) {
		$bIsBot = false;

		$bCheckIpHost = is_null( $sReqUserAgent );
		if ( !$bCheckIpHost ) {
			$aBotUserAgents = array_map(
				function ( $sAgent ) {
					return preg_quote( $sAgent, '#' );
				},
				$aBotUserAgents
			);
			$bCheckIpHost = (bool)preg_match( sprintf( '#%s#i', implode( '|', $aBotUserAgents ) ), $sReqUserAgent );
		}

		if ( $bCheckIpHost ) {
			$sHost = @gethostbyaddr( $sReqIp ); // returns the ip on failure
			$bIsBot = !empty( $sHost ) && ( $sHost != $sReqIp )
					  && preg_match( $sBotHostPattern, $sHost )
					  && gethostbyname( $sHost ) === $sReqIp;
		}
		return $bIsBot;
	}

	/**
	 * @param string $sKey
	 * @return string
	 */
	private function getPrefixedStoreKey( $sKey ) {
		return 'odp_'.$sKey;
	}

	/**
	 * @param string $sIp
	 * @return bool
	 * @deprecated
	 */
	public function isIp_Sucuri( $sIp ) {
		return false;
	}
}