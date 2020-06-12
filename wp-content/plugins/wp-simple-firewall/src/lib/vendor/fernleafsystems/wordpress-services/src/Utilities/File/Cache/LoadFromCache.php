<?php

namespace FernleafSystems\Wordpress\Services\Utilities\File\Cache;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class LoadFromCache
 * @package FernleafSystems\Wordpress\Services\Utilities\File\Cache
 */
class LoadFromCache extends Base {

	/**
	 * @return bool
	 */
	public function load() {
		$bSuccess = false;
		$oFS = Services::WpFs();

		$oDef = $this->getCacheDef();
		$sFile = $this->getCacheFile();
		$nExpireBoundary = Services::Request()
								   ->carbon()
								   ->subSeconds( $oDef->expiration )->timestamp;
		if ( $oFS->exists( $sFile ) && $oFS->getModifiedTime( $sFile ) > $nExpireBoundary ) {
			$sJson = $oFS->getFileContent( $sFile, true );
			if ( !empty( $sJson ) ) {
				if ( $oDef->touch_on_load ) {
					$oFS->touch( $sFile );
				}
				$oDef->data = json_decode( $sJson, true );
				$bSuccess = is_array( $oDef->data );
			}
		}
		return $bSuccess;
	}
}