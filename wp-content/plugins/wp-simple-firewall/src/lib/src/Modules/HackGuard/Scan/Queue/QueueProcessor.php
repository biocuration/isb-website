<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\HackGuard\Scan\Queue;

use FernleafSystems\Wordpress\Plugin\Shield;
use FernleafSystems\Wordpress\Plugin\Shield\Databases\ScanQueue;
use FernleafSystems\Wordpress\Services\Services;
use FernleafSystems\Wordpress\Services\Utilities;

class QueueProcessor extends Utilities\BackgroundProcessing\BackgroundProcess {

	use Shield\Modules\ModConsumer;

	/**
	 * Get batch
	 *
	 * @return \stdClass Return the first batch from the queue
	 */
	protected function get_batch() {
		/** @var ScanQueue\Select $oSel */
		$oSel = $this->getDbHandler()->getQuerySelector();

		$oEntry = $oSel->filterByNotStarted()
					   ->filterByNotFinished()
					   ->setOrderBy( 'id', 'ASC', true )
					   ->first();

		$oBatch = new \stdClass();
		$oBatch->key = $oEntry->id;
		$oBatch->data = [ $oEntry ];
		return $oBatch;
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param ScanQueue\EntryVO $oEntry Queue item to iterate over.
	 * @return mixed
	 */
	protected function task( $oEntry ) {
		$oEntry->started_at = Services::Request()->ts();
		/** @var ScanQueue\Update $oUpd */
		$oUpd = $this->getDbHandler()->getQueryUpdater();
		$oUpd->setStarted( $oEntry );

		try {
			( new ScanExecute() )
				->setMod( $this->getMod() )
				->execute( $oEntry );
		}
		catch ( \Exception $oE ) {
//			error_log( $oE->getMessage() );
		}

		$oUpd->setFinished( $oEntry );
		return $oEntry;
	}

	/**
	 * Complete.
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();

		( new CompleteQueue() )
			->setDbHandler( $this->getDbHandler() )
			->setMod( $this->getMod() )
			->complete();
	}

	/**
	 * Delete queue
	 *
	 * @param string $key Key.
	 * @return $this
	 */
	public function delete( $key ) {
		/** @var ScanQueue\Delete $oDel */
		$oDel = $this->getDbHandler()->getQueryDeleter();
		$oDel->deleteById( $key );
		return $this;
	}

	/**
	 * Is queue empty
	 *
	 * @return bool
	 */
	protected function is_queue_empty() {
		/** @var ScanQueue\Select $oSel */
		$oSel = $this->getDbHandler()->getQuerySelector();
		$nUnfinished = $oSel->filterByNotStarted()
							->filterByNotFinished()
							->count();
		return $nUnfinished === 0;
	}

	/**
	 * Save queue
	 *
	 * @return $this
	 */
	public function save() {

		if ( is_array( $this->data ) ) {
			/** @var ScanQueue\Insert $oInsert */
			$oInsert = $this->getDbHandler()->getQueryInserter();
			foreach ( $this->data as $nKey => $oEntry ) {
				/** @var ScanQueue\EntryVO $oEntry */
				if ( $oEntry instanceof ScanQueue\EntryVO ) {
					$oInsert->insert( $oEntry );
				}
			}
		}

		$this->data = []; // critical to preventing duplicate entries
		return $this;
	}

	/**
	 * Update queue
	 *
	 * @param string              $key  Key.
	 * @param ScanQueue\EntryVO[] $data Data.
	 * @return $this
	 */
	public function update( $key, $data ) {
		/** @var ScanQueue\Update $oUpd */
		$oUpd = $this->getDbHandler()->getQueryUpdater();
		$oToUpdate = array_shift( $data );
		$oUpd->updateEntry( $oToUpdate );
		return $this;
	}

	/**
	 */
	public function handleExpiredItems() {
		$nBoundary = Services::Request()
							 ->carbon()
							 ->subSeconds( $this->getExpirationInterval() )->timestamp;
		$this->getDbHandler()->deleteRowsOlderThan( $nBoundary );
	}

	/**
	 * @return ScanQueue\Handler
	 */
	public function getDbHandler() {
		/** @var \ICWP_WPSF_FeatureHandler_HackProtect $oMod */
		$oMod = $this->getMod();
		return $oMod->getDbHandler_ScanQueue();
	}
}
