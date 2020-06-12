<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\HackGuard\Lib\FileLocker\Ops;

use FernleafSystems\Wordpress\Plugin\Shield\Databases\FileLocker;
use FernleafSystems\Wordpress\Services\Utilities\File\Compare\CompareHash;

class AssessLocks extends BaseOps {

	/**
	 * @return int[]
	 */
	public function run() {
		/** @var \ICWP_WPSF_FeatureHandler_HackProtect $oMod */
		$oMod = $this->getMod();
		/** @var FileLocker\Update $oUpd */
		$oUpd = $oMod->getDbHandler_FileLocker()->getQueryUpdater();

		$this->removeDuplicates();

		$aProblemIds = [];
		foreach ( $this->getFileLocks() as $oLock ) {
			try {
				if ( ( new CompareHash() )->isEqualFileSha1( $oLock->file, $oLock->hash_original ) ) {
					if ( !empty( $oLock->hash_current ) ) {
						$oUpd->updateCurrentHash( $oLock, '' );
					}
				}
				else {
					$sFileHash = hash_file( 'sha1', $oLock->file );
					if ( empty( $oLock->hash_current ) || !hash_equals( $oLock->hash_current, $sFileHash ) ) {
						$oUpd->updateCurrentHash( $oLock, $sFileHash );
						$aProblemIds[] = $oLock->id;
					}
				}
			}
			catch ( \InvalidArgumentException $oE ) {
				$oUpd->markProblem( $oLock );
				$aProblemIds[] = $oLock->id;
			}
		}
		$this->clearFileLocksCache();
		return $aProblemIds;
	}

	private function removeDuplicates() {
		$aPaths = [];
		foreach ( $this->getFileLocks() as $oLock ) {
			if ( in_array( $oLock->file, $aPaths ) ) {
				/** @var \ICWP_WPSF_FeatureHandler_HackProtect $oMod */
				$oMod = $this->getMod();
				$oMod->getDbHandler_FileLocker()
					 ->getQueryDeleter()
					 ->deleteById( $oLock->id );
			}
			else {
				$aPaths[] = $oLock->file;
			}
		}
		if ( count( $this->getFileLocks() ) != count( $aPaths ) ) {
			$this->clearFileLocksCache();
		}
	}
}