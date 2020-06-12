<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Tables\Render;

use FernleafSystems\Wordpress\Plugin\Shield\Scans;

class ScanMal extends ScanBase {

	/**
	 * @param array $aItem
	 * @return string
	 */
	public function column_path( $aItem ) {
		$aButtons = [
			$this->getActionButton_Ignore( $aItem[ 'id' ] ),
		];
		if ( $aItem[ 'can_repair' ] ) {
			$aButtons[] = $this->getActionButton_Repair( $aItem[ 'id' ] );
		}
		else {
			$aButtons[] = $this->getActionButton_Delete( $aItem[ 'id' ] );
		}
		if ( !empty( $aItem[ 'href_download' ] ) ) {
			$aButtons[] = $this->getActionButton_DownloadFile( $aItem[ 'href_download' ] );
		}
		return parent::column_path( $aItem ).$this->buildActions( $aButtons );
	}

	/**
	 * @return array
	 */
	protected function get_bulk_actions() {
		return [
			'ignore' => __( 'Ignore', 'wp-simple-firewall' ),
			'delete' => __( 'Delete', 'wp-simple-firewall' ),
			'repair' => __( 'Repair', 'wp-simple-firewall' ),
		];
	}

	/**
	 * @return array
	 */
	public function get_columns() {
		return array_merge(
			[ 'cb' => '&nbsp;' ],
			parent::get_columns()
		);
	}
}