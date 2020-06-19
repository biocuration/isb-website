<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin;

use FernleafSystems\Utilities\Data\Adapter\StdClassAdapter;
use FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin\VOs\PluginInfoVO;

/**
 * Class Api
 * @package FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin
 * @property array $fields
 */
class Api {

	use Base;
	use StdClassAdapter;

	/**
	 * @return PluginInfoVO
	 * @throws \Exception
	 */
	public function getInfo() {
		return $this->run( 'plugin_information' );
	}

	/**
	 * @return PluginInfoVO
	 * @throws \Exception
	 * @deprecated 0.1.19
	 */
	public function getPluginInfo() {
		return $this->getInfo();
	}

	/**
	 * @param string $sCmd
	 * @return PluginInfoVO
	 * @throws \Exception
	 */
	public function run( $sCmd ) {
		include_once( ABSPATH.'wp-admin/includes/plugin-install.php' );

		$aParams = $this->getRawDataAsArray();
		$aParams[ 'slug' ] = $this->getWorkingSlug();
		$oResponse = \plugins_api( $sCmd, $aParams );

		if ( \is_wp_error( $oResponse ) ) {
			throw new \Exception( sprintf( '[PluginsApi Error] %s', $oResponse->get_error_message() ) );
		}
		elseif ( !\is_object( $oResponse ) ) {
			throw new \Exception( sprintf( '[PluginsApi Error] %s', 'Did not return an expected Object' ) );
		}

		return ( new PluginInfoVO() )->applyFromArray( (array)$oResponse );
	}
}