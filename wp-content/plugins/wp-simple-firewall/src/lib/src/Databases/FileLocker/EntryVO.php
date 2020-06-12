<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Databases\FileLocker;

use FernleafSystems\Wordpress\Plugin\Shield\Databases\Base;

/**
 * Class EntryVO
 * @property string $file
 * @property string $hash_original
 * @property string $hash_current
 * @property string $content
 * @property int    $public_key_id
 * @property int    $detected_at
 * @property int    $reverted_at
 * @property int    $notified_at
 * @property int    $updated_at
 */
class EntryVO extends Base\EntryVO {

	/**
	 * @param string $sProperty
	 * @return mixed
	 */
	public function __get( $sProperty ) {

		$mValue = parent::__get( $sProperty );

		switch ( $sProperty ) {

			case 'content':
			case 'file':
				$mValue = base64_decode( $mValue );
				break;

			default:
				break;
		}
		return $mValue;
	}

	/**
	 * @param string $sProperty
	 * @param mixed  $mValue
	 * @return $this
	 */
	public function __set( $sProperty, $mValue ) {

		switch ( $sProperty ) {

			case 'content':
			case 'file':
				$mValue = base64_encode( $mValue );
				break;

			default:
				break;
		}

		return parent::__set( $sProperty, $mValue );
	}
}