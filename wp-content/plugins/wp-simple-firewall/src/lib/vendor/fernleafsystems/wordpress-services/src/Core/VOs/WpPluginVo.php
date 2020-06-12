<?php

namespace FernleafSystems\Wordpress\Services\Core\VOs;

use FernleafSystems\Wordpress\Services\Services;
use FernleafSystems\Wordpress\Services\Utilities\WpOrg\Plugin;

/**
 * Class WpPluginVo
 * @package FernleafSystems\Wordpress\Services\Core\VOs
 * @property string                  PluginURI
 * @property bool                    Network
 * @property string                  Title
 * @property string                  AuthorName
 * Extended Properties:
 * @property string                  $id
 * @property string                  $slug
 * @property string                  $plugin
 * @property string                  $new_version
 * @property string                  $url
 * @property string                  $package      - the update package URL
 * Custom Properties:
 * @property string                  $file
 * @property bool                    $svn_uses_tags
 * @property Plugin\VOs\PluginInfoVO $wp_info
 */
class WpPluginVo extends WpBaseVo {

	/**
	 * WpPluginVo constructor.
	 * @param string $sBaseFile
	 * @throws \Exception
	 */
	public function __construct( $sBaseFile ) {
		$oWpPlugins = Services::WpPlugins();
		$aPlug = $oWpPlugins->getPlugin( $sBaseFile );
		if ( empty( $aPlug ) ) {
			throw new \Exception( sprintf( 'Plugin file %s does not exist', $sBaseFile ) );
		}
		$this->applyFromArray( $aPlug );
		$this->file = $sBaseFile;
		$this->active = $oWpPlugins->isActive( $sBaseFile );
	}

	/**
	 * @param string $sProperty
	 * @return mixed
	 */
	public function __get( $sProperty ) {

		$mVal = parent::__get( $sProperty );

		switch ( $sProperty ) {

			case 'unique_id':
				$mVal = $this->file;
				break;

			case 'version':
				$mVal = $this->Version;
				break;

			case 'svn_uses_tags':
				if ( is_null( $mVal ) ) {
					$mVal = ( new Plugin\Versions() )
						->setWorkingSlug( $this->slug )
						->exists( $this->Version, true );
					$this->svn_uses_tags = $mVal;
				}
				break;

			default:
				break;
		}

		return $mVal;
	}

	/**
	 * @return string
	 */
	public function getInstallDir() {
		return wp_normalize_path( trailingslashit( dirname( path_join( WP_PLUGIN_DIR, $this->file ) ) ) );
	}

	/**
	 * @return bool
	 */
	public function isWpOrg() {
		$this->id; // loads the data
		return isset( $this->id ) ? strpos( $this->id, 'w.org/' ) === 0 : false;
	}

	/**
	 * @return array
	 */
	protected function getExtendedData() {
		return Services::WpPlugins()->getExtendedData( $this->file );
	}

	/**
	 * @return string[]
	 */
	protected function getExtendedDataSlugs() {
		return array_merge( parent::getExtendedDataSlugs(), [
			'id',
			'slug',
			'plugin',
			'package',
			'url',
		] );
	}

	/**
	 * @return false|Plugin\VOs\PluginInfoVO
	 */
	protected function loadWpInfo() {
		try {
			$oInfo = ( new Plugin\Api() )
				->setWorkingSlug( $this->slug )
				->getInfo();
		}
		catch ( \Exception $oE ) {
			$oInfo = false;
		}
		return $oInfo;
	}
}