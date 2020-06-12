<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\HackGuard\Lib\FileLocker\Ops;

use FernleafSystems\Wordpress\Plugin\Shield\Databases\FileLocker;

/**
 * Class DeleteFileLock
 * @package FernleafSystems\Wordpress\Plugin\Shield\Modules\HackGuard\Lib\FileLocker\Ops
 */
class DeleteFileLock extends BaseOps {

	/**
	 * @param FileLocker\EntryVO $oLock
	 * @return bool
	 */
	public function delete( $oLock = null ) {
		/** @var \ICWP_WPSF_FeatureHandler_HackProtect $oMod */
		$oMod = $this->getMod();
		if ( empty( $oLock ) ) {
			$oLock = $this->findLockRecordForFile();
		}
		$bSuccess = $oLock instanceof FileLocker\EntryVO
					&& $oMod->getDbHandler_FileLocker()
							->getQueryDeleter()
							->deleteEntry( $oLock );
		if ( $bSuccess ) {
			$this->clearFileLocksCache();
		}
		return $bSuccess;
	}
}