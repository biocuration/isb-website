<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Databases\Traffic;

use FernleafSystems\Wordpress\Plugin\Shield\Databases\Base;

/**
 * Class EntryVO
 * @property string $rid
 * @property int    $uid
 * @property string $ip
 * @property string $path
 * @property string $code
 * @property string $ua
 * @property string $verb
 * @property bool   $trans
 */
class EntryVO extends Base\EntryVO {

	/**
	 * @param string $sProperty
	 * @return mixed
	 */
	public function __get( $sProperty ) {
		switch ( $sProperty ) {

			case 'ip':
				$mVal = inet_ntop( parent::__get( $sProperty ) );
				break;

			default:
				$mVal = parent::__get( $sProperty );
		}
		return $mVal;
	}

	/**
	 * @param string $sProperty
	 * @param mixed  $mValue
	 * @return $this
	 */
	public function __set( $sProperty, $mValue ) {

		switch ( $sProperty ) {

			case 'ip':
				$mValue = inet_pton( $mValue );
				break;

			default:
				break;
		}

		return parent::__set( $sProperty, $mValue );
	}
}