<?php

namespace FernleafSystems\Wordpress\Services\Utilities\File\Cache;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class StoreToCache
 * @package FernleafSystems\Wordpress\Services\Utilities\File\Cache
 */
class StoreToCache extends Base {

	/**
	 * @return bool
	 */
	public function store() {
		$bSuccess = false;
		$oDef = $this->getCacheDef();
		if ( is_array( $oDef->data ) && $this->prepCacheDir() ) {
			$bSuccess = Services::WpFs()->putFileContent(
				$this->getCacheFile(),
				json_encode( $oDef->data ),
				true
			);
		}
		return $bSuccess;
	}
}