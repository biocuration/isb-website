<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Theme\VOs;

use FernleafSystems\Utilities\Data\Adapter\StdClassAdapter;

/**
 * Class ThemeInfoVO
 * @package FernleafSystems\Wordpress\Services\Utilities\WpOrg\Theme\VOs
 * @property string   name
 * @property string   slug
 * @property string   version
 * @property string   preview_url
 * @property string[] author                     - user_nicename / profile / avatar / display_name
 * @property string   screenshot_url             - URL
 * @property string[] versions                   - key is version, value is ZIP URL
 * @property string   requires
 * @property string   requires_php
 * @property int      rating
 * @property int[]    ratings                    - 1, 2, 3, 4, 5
 * @property int      num_ratings
 * @property int      downloaded
 * @property string   last_updated
 * @property string   homepage                   - URL
 * @property array    sections                   - description /
 * @property string   download_link
 * @property string[] tags                       - theme tags, not versions
 */
class ThemeInfoVO {

	use StdClassAdapter;

	/**
	 * @return float
	 */
	public function getNetPromoterScore() {
		$aRs = $this->ratings;
		return ( $aRs[ 5 ] - ( $aRs[ 1 ] + $aRs[ 2 ] + $aRs[ 3 ] ) )/array_sum( $aRs );
	}
}