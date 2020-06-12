<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\IPs\BotTrack;

use FernleafSystems\Wordpress\Services\Services;

class Track404 extends Base {

	const OPT_KEY = 'track_404';

	protected function process() {
		add_action( 'template_redirect', function () {
			if ( is_404() ) {
				$this->doTransgression();
			}
		} );
	}
}
