<?php

namespace FernleafSystems\Wordpress\Services\Utilities\WpOrg\Theme;

use FernleafSystems\Wordpress\Services\Utilities\HttpUtil;
use FernleafSystems\Wordpress\Services\Utilities\WpOrg\Base\PluginThemeVersionsBase;

class Versions extends PluginThemeVersionsBase {

	use Base;

	/**
	 * @return Api
	 */
	protected function getApi() {
		return new Api();
	}

	/**
	 * @param string $sVersion
	 * @param bool   $bVerifyUrl
	 * @return bool
	 */
	public function exists( $sVersion, $bVerifyUrl = false ) {
		$bExists = in_array( $sVersion, $this->all() );
		if ( $bExists && $bVerifyUrl ) {
			try {
				( new HttpUtil() )->checkUrl( Repo::GetUrlForThemeVersion( $this->getWorkingSlug(), $sVersion ) );
			}
			catch ( \Exception $oE ) {
				$bExists = false;
			}
		}
		return $bExists;
	}
}