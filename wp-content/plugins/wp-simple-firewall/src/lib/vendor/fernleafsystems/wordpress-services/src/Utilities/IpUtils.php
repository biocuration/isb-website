<?php

namespace FernleafSystems\Wordpress\Services\Utilities;

use FernleafSystems\Wordpress\Services\Services;
use FernleafSystems\Wordpress\Services\Utilities;
use FernleafSystems\Wordpress\Services\Utilities\Integrations\Ipify;
use FernleafSystems\Wordpress\Services\Utilities\Net\FindSourceFromIp;

/**
 * Class IpUtils
 * @package FernleafSystems\Wordpress\Services\Utilities
 */
class IpUtils {

	/**
	 * @var Utilities\Net\VisitorIpDetection
	 */
	private $oIpDetector;

	/**
	 * @var string - used to override IP Detector
	 */
	private $sIp;

	/**
	 * @var string[]
	 */
	private $aMyIps;

	/**
	 * @var IpUtils
	 */
	protected static $oInstance = null;

	/**
	 * @return IpUtils
	 */
	public static function GetInstance() {
		if ( is_null( self::$oInstance ) ) {
			self::$oInstance = new self();
		}
		return self::$oInstance;
	}

	/**
	 * Checks if an IPv4 or IPv6 address is contained in the list of given IPs or subnets.
	 * @param string       $requestIp IP to check
	 * @param string|array $ips       List of IPs or subnets (can be a string if only a single one)
	 * @return bool Whether the IP is valid
	 * @throws \Exception When IPV6 support is not enabled
	 */
	public static function checkIp( $requestIp, $ips ) {
		if ( !is_array( $ips ) ) {
			$ips = [ $ips ];
		}
		$method = substr_count( $requestIp, ':' ) > 1 ? 'checkIp6' : 'checkIp4';
		foreach ( $ips as $ip ) {
			if ( self::$method( $requestIp, $ip ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Compares two IPv4 addresses.
	 * In case a subnet is given, it checks if it contains the request IP.
	 * @param string $requestIp IPv4 address to check
	 * @param string $ip        IPv4 address or subnet in CIDR notation
	 * @return bool Whether the IP is valid
	 */
	public static function checkIp4( $requestIp, $ip ) {
		if ( false !== strpos( $ip, '/' ) ) {
			if ( '0.0.0.0/0' === $ip ) {
				return true;
			}
			list( $address, $netmask ) = explode( '/', $ip, 2 );
			if ( $netmask < 1 || $netmask > 32 ) {
				return false;
			}
		}
		else {
			$address = $ip;
			$netmask = 32;
		}
		return 0 === substr_compare( sprintf( '%032b', ip2long( $requestIp ) ), sprintf( '%032b', ip2long( $address ) ), 0, $netmask );
	}

	/**
	 * Compares two IPv6 addresses.
	 * In case a subnet is given, it checks if it contains the request IP.
	 * @param string $requestIp IPv6 address to check
	 * @param string $ip        IPv6 address or subnet in CIDR notation
	 * @return bool Whether the IP is valid
	 * @throws \Exception When IPV6 support is not enabled
	 * @author David Soria Parra <dsp at php dot net>
	 * @see    https://github.com/dsp/v6tools
	 */
	public static function checkIp6( $requestIp, $ip ) {
		if ( !( ( extension_loaded( 'sockets' ) && defined( 'AF_INET6' ) ) || @inet_pton( '::1' ) ) ) {
			throw new \Exception( 'Unable to check Ipv6. Check that PHP was not compiled with option "disable-ipv6".' );
		}
		if ( false !== strpos( $ip, '/' ) ) {
			list( $address, $netmask ) = explode( '/', $ip, 2 );
			if ( $netmask < 1 || $netmask > 128 ) {
				return false;
			}
		}
		else {
			$address = $ip;
			$netmask = 128;
		}
		$bytesAddr = unpack( 'n*', inet_pton( $address ) );
		$bytesTest = unpack( 'n*', inet_pton( $requestIp ) );
		for ( $i = 1, $ceil = ceil( $netmask/16 ) ; $i <= $ceil ; ++$i ) {
			$left = $netmask - 16*( $i - 1 );
			$left = ( $left <= 16 ) ? $left : 16;
			$mask = ~( 0xffff >> $left ) & 0xffff;
			if ( ( $bytesAddr[ $i ] & $mask ) != ( $bytesTest[ $i ] & $mask ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * @param string $sIp
	 * @return bool|int
	 */
	public function getIpVersion( $sIp ) {
		if ( filter_var( $sIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
			return 4;
		}
		if ( filter_var( $sIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
			return 6;
		}
		return false;
	}

	/**
	 * @param string $sIp
	 * @return string
	 */
	public function getIpWhoisLookup( $sIp ) {
		return sprintf( 'https://apps.db.ripe.net/db-web-ui/#/query?bflag&searchtext=%s#resultsSection', $sIp );
	}

	/**
	 * @param string $sIp
	 * @return string
	 */
	public function getIpInfo( $sIp ) {
		return sprintf( 'https://redirect.li/map/?ip=%s', $sIp );
	}

	/**
	 * @param string $sIp
	 * @return string
	 */
	public function getIpGeoInfo( $sIp = null ) {
		return Services::HttpRequest()->getContent(
			sprintf( 'http://ip6.me/api/%s', empty( $sIp ) ? '' : '/'.$sIp )
		);
	}

	/**
	 * @return Utilities\Net\VisitorIpDetection
	 */
	public function getIpDetector() {
		if ( !$this->oIpDetector instanceof Utilities\Net\VisitorIpDetection ) {
			$this->oIpDetector = new Utilities\Net\VisitorIpDetection();
		}
		return $this->oIpDetector;
	}

	/**
	 * @param bool $bAsHuman
	 * @return int|string|bool - visitor IP Address as IP2Long
	 */
	public function getRequestIp( $bAsHuman = true ) {
		$sIP = empty( $this->sIp ) ? $this->getIpDetector()->getIP() : $this->sIp;

		// If it's IPv6 we never return as long (we can't!)
		if ( !empty( $sIP ) || $bAsHuman || $this->getIpVersion( $sIP ) == 6 ) {
			return $sIP;
		}

		return ip2long( $sIP );
	}

	/**
	 * @param $sIP
	 * @return bool
	 */
	public function isPrivateIP( $sIP ) {
		return $this->isValidIp( $sIP )
			   && !$this->isValidIp_PublicRemote( $sIP );
	}

	/**
	 * @param string $sIP
	 * @return bool
	 */
	public function isTrueLoopback( $sIP ) {
		try {
			$bLB = ( $this->getIpVersion( $sIP ) == 4 && $this->checkIp4( $sIP, '127.0.0.0/8' ) )
				   || ( $this->getIpVersion( $sIP == 6 ) && $this->checkIp6( $sIP, '::1/128' ) );
		}
		catch ( \Exception $e ) {
			$bLB = false;
		}
		return $bLB;
	}

	/**
	 * @return bool
	 */
	public function isLoopback() {
		return in_array( $this->getRequestIp(), $this->getServerPublicIPs() );
	}

	/**
	 * @return bool
	 */
	public function isSupportedIpv6() {
		return ( extension_loaded( 'sockets' ) && defined( 'AF_INET6' ) ) || @inet_pton( '::1' );
	}

	/**
	 * @param string $sIp
	 * @param bool   $flags
	 * @return bool
	 */
	public function isValidIp( $sIp, $flags = null ) {
		return filter_var( trim( $sIp ), FILTER_VALIDATE_IP, $flags );
	}

	/**
	 * @param string $sIp
	 * @return bool
	 */
	public function isValidIp4Range( $sIp ) {
		$bIsRange = false;
		if ( strpos( $sIp, '/' ) ) {
			list( $sIp, $sCIDR ) = explode( '/', $sIp );
			$bIsRange = $this->isValidIp( $sIp ) && ( (int)$sCIDR >= 0 && (int)$sCIDR <= 32 );
		}
		return $bIsRange;
	}

	/**
	 * @param string $sIp
	 * @return bool
	 */
	public function isValidIp6Range( $sIp ) {
		$bIsRange = false;
		if ( strpos( $sIp, '/' ) ) {
			list( $sIp, $sCIDR ) = explode( '/', $sIp );
			$bIsRange = $this->isValidIp( $sIp ) && ( (int)$sCIDR >= 0 && (int)$sCIDR <= 128 );
		}
		return $bIsRange;
	}

	/**
	 * @param string $sIp
	 * @return bool
	 */
	public function isValidIpOrRange( $sIp ) {
		return $this->isValidIp_PublicRemote( $sIp ) || $this->isValidIpRange( $sIp );
	}

	/**
	 * Assumes a valid IPv4 address is provided as we're only testing for a whether the IP is public or not.
	 * @param string $sIp
	 * @return bool
	 */
	public function isValidIp_PublicRange( $sIp ) {
		return $this->isValidIp( $sIp, FILTER_FLAG_NO_PRIV_RANGE );
	}

	/**
	 * @param string $sIp
	 * @return bool
	 */
	public function isValidIp_PublicRemote( $sIp ) {
		return $this->isValidIp( $sIp, ( FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) );
	}

	/**
	 * @param string $sIp
	 * @return bool
	 */
	public function isValidIpRange( $sIp ) {
		return $this->isValidIp4Range( $sIp ) || $this->isValidIp6Range( $sIp );
	}

	/**
	 * @param bool $bForceRefresh
	 * @return string[]
	 */
	public function getServerPublicIPs( $bForceRefresh = false ) {
		if ( $bForceRefresh || empty( $this->aMyIps ) ) {

			$aIPs = Services::WpGeneral()->getOption( 'aptoweb_my_server_ips' );
			if ( !empty( $aIPs ) ) {
				Services::WpGeneral()->deleteOption( 'aptoweb_my_server_ips' );
				Utilities\Options\Transient::Set( 'my_server_ips', $aIPs, WEEK_IN_SECONDS );
			}
			$aIPs = Utilities\Options\Transient::Get( 'my_server_ips' );

			if ( empty( $aIPs ) || !is_array( $aIPs ) || empty( $aIPs[ 'check_at' ] ) ) {
				$aIPs = [
					'check_at' => 0,
					'hash'     => '',
					'ips'      => []
				];
			}

			$nAge = Services::Request()->ts() - $aIPs[ 'check_at' ];
			$bExpired = ( $nAge > HOUR_IN_SECONDS )
						&& ( Services::Data()->getServerHash() != $aIPs[ 'hash' ] || $nAge > WEEK_IN_SECONDS );
			if ( $bForceRefresh || $bExpired ) {
				$aIPs = [
					'check_at' => Services::Request()->ts(),
					'hash'     => Services::Data()->getServerHash(),
					'ips'      => array_filter(
						( new Ipify\Api() )->getMyIps(),
						function ( $sIP ) {
							return $this->isValidIp_PublicRemote( $sIP );
						}
					)
				];
				Utilities\Options\Transient::Set( 'my_server_ips', $aIPs, WEEK_IN_SECONDS );
			}

			$this->aMyIps = $aIPs[ 'ips' ];
		}
		return $this->aMyIps;
	}

	/**
	 * @param $sIP
	 * @return string|null
	 */
	public function determineSourceFromIp( $sIP ) {
		return ( new FindSourceFromIp() )->run( $sIP );
	}

	/**
	 * @param Net\VisitorIpDetection $oDetector
	 * @return $this
	 */
	public function setIpDetector( Utilities\Net\VisitorIpDetection $oDetector ) {
		$this->oIpDetector = $oDetector;
		return $this;
	}

	/**
	 * Override the Detector with this IP.
	 * @param string $sIp
	 * @return $this
	 */
	public function setRequestIpAddress( $sIp ) {
		$this->sIp = $sIp;
		return $this;
	}
}