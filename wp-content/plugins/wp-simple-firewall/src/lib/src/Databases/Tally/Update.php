<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Databases\Tally;

use FernleafSystems\Wordpress\Plugin\Shield\Databases\Base;

class Update extends Base\Update {

	/**
	 * @param EntryVO $oStat
	 * @param int     $nAdditional
	 * @return bool
	 */
	public function incrementTally( $oStat, $nAdditional ) {
		return $this->updateEntry( $oStat, [ 'tally' => $oStat->tally + $nAdditional, ] );
	}
}