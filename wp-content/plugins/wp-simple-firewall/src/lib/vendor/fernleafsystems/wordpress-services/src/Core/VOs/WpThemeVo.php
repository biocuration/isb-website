<?php

namespace FernleafSystems\Wordpress\Services\Core\VOs;

use FernleafSystems\Wordpress\Services\Services;
use FernleafSystems\Wordpress\Services\Utilities\WpOrg\Theme;

/**
 * Class WpThemeVo
 * @package FernleafSystems\Wordpress\Services\Core\VOs
 * @property string                      $theme        - the stylesheet
 * @property string                      $stylesheet   - the stylesheet
 * @property \WP_Theme                   $wp_theme
 * @property Theme\VOs\ThemeInfoVO|false $wp_info      - wp.org theme info
 * @property string                      $new_version
 * @property string                      $url
 * @property string                      $package
 * @property string                      $requires
 * @property string                      $requires_php
 * @property bool                        $is_child
 * @property bool                        $is_parent
 */
class WpThemeVo extends WpBaseVo {

	/**
	 * WpPluginVo constructor.
	 * @param string $sStylesheet - the name of the theme folder.
	 * @throws \Exception
	 */
	public function __construct( $sStylesheet ) {
		$oWpTheme = Services::WpThemes();
		$oT = $oWpTheme->getTheme( $sStylesheet );
		if ( empty( $oT ) ) {
			throw new \Exception( sprintf( 'Theme file %s does not exist', $sStylesheet ) );
		}
		$this->wp_theme = $oT;
		$this->stylesheet = $sStylesheet;
		$this->active = $oWpTheme->isActive( $sStylesheet );
		$this->is_child = $this->active && $oWpTheme->isActiveThemeAChild();
		$this->is_parent = !$this->active && $oWpTheme->isActiveParent( $sStylesheet );
	}

	/**
	 * @param string $sProperty
	 * @return mixed
	 */
	public function __get( $sProperty ) {

		$mVal = parent::__get( $sProperty );

		if ( in_array( $sProperty, $this->getWpThemeKeys() ) ) {
			$mVal = $this->wp_theme->get( $sProperty );
		}
		else {
			switch ( $sProperty ) {

				case 'unique_id':
					$mVal = $this->stylesheet;
					break;

				case 'version':
					if ( is_null( $mVal ) ) {
						$mVal = $this->Version;
					}
					break;

				default:
					break;
			}
		}

		return $mVal;
	}

	/**
	 * @return string[]
	 */
	private function getWpThemeKeys() {
		return [
			'Name',
			'ThemeURI',
			'Description',
			'Author',
			'AuthorURI',
			'Version',
			'Template',
			'Status',
			'Tags',
			'TextDomain',
			'DomainPath',
		];
	}

	/**
	 * @return string
	 */
	public function getInstallDir() {
		return wp_normalize_path( trailingslashit( $this->wp_theme->get_stylesheet_directory() ) );
	}

	/**
	 * @return bool
	 */
	public function isWpOrg() {
		$this->wp_info;
		return !empty( $this->wp_info );
	}

	/**
	 * @return array
	 */
	protected function getExtendedData() {
		return Services::WpThemes()->getExtendedData( $this->stylesheet );
	}

	/**
	 * @return string[]
	 */
	protected function getExtendedDataSlugs() {
		return array_merge( parent::getExtendedDataSlugs(), [
			'theme',
			'package',
			'requires',
			'requires_php',
			'url',
		] );
	}

	/**
	 * @return false|Theme\VOs\ThemeInfoVO
	 */
	protected function loadWpInfo() {
		try {
			$oInfo = ( new Theme\Api() )
				->setWorkingSlug( $this->stylesheet )
				->getInfo();
		}
		catch ( \Exception $oE ) {
			$oInfo = false;
		}
		return $oInfo;
	}
}