<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\CommentsFilter;

use FernleafSystems\Wordpress\Plugin\Shield\Modules\Base;

class Options extends Base\ShieldOptions {

	/**
	 * @return string[]
	 */
	public function getHumanSpamFilterItems() {
		$aDefault = $this->getOptDefault( 'human_spam_items' );
		$aItems = apply_filters(
			$this->getCon()->prefix( 'human_spam_items' ),
			$this->getOpt( 'human_spam_items', [] )
		);
		return is_array( $aItems ) ? array_intersect( $aDefault, $aItems ) : $aDefault;
	}

	/**
	 * @return int
	 */
	public function getTokenCooldown() {
		if ( (int)$this->getOpt( 'comments_cooldown', 10 ) < 1 ) {
			$this->resetOptToDefault( 'comments_cooldown' );
		}
		return (int)max( 0,
			apply_filters(
				$this->getCon()->prefix( 'comments_cooldown' ),
				$this->getOpt( 'comments_cooldown', 10 )
			)
		);
	}

	/**
	 * @return int
	 */
	public function getTokenExpireInterval() {
		return (int)max( 0,
			apply_filters(
				$this->getCon()->prefix( 'comments_expire' ),
				$this->getDef( 'comments_expire' )
			)
		);
	}
}