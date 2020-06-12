<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Databases\Events;

use FernleafSystems\Wordpress\Plugin\Shield\Databases\Base;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\Events\Options;
use FernleafSystems\Wordpress\Services\Services;

class Handler extends Base\Handler {

	/**
	 * @param $aEvents - array of events: key event slug, value created_at timestamp
	 */
	public function commitEvents( $aEvents ) {
		foreach ( $aEvents as $sEvent => $nTs ) {
			$this->commitEvent( $sEvent, 1, $nTs );
		}
	}

	/**
	 * @param string $sEvent
	 * @param null   $nTs
	 * @param int    $nCount
	 * @return bool
	 */
	public function commitEvent( $sEvent, $nCount = 1, $nTs = null ) {
		if ( empty( $nTs ) || !is_numeric( $nTs ) ) {
			$nTs = Services::Request()->ts();
		}

		/** @var EntryVO $oEvt */
		$oEvt = $this->getVo();
		$oEvt->event = $sEvent;
		$oEvt->count = max( 1, (int)$nCount );
		$oEvt->created_at = max( 1, $nTs );
		/** @var Insert $oQI */
		$oQI = $this->getQueryInserter();
		return $oQI->insert( $oEvt );
	}

	/**
	 * @return string[]
	 */
	protected function getDefaultColumnsDefinition() {
		/** @var Options $oOpts */
		$oOpts = $this->getOptions();
		return $oOpts->getDbColumns_Events();
	}

	/**
	 * @return string
	 */
	protected function getDefaultTableName() {
		/** @var Options $oOpts */
		$oOpts = $this->getOptions();
		return $oOpts->getDbTable_Events();
	}

	/**
	 * @return string
	 */
	protected function getDefaultCreateTableSql() {
		return "CREATE TABLE %s (
			id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			event varchar(50) NOT NULL DEFAULT 'none' COMMENT 'Event ID',
			count int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Total',
			created_at int(15) UNSIGNED NOT NULL DEFAULT 0,
			deleted_at int(15) UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY  (id)
		) %s;";
	}
}