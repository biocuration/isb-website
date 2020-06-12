<?php

namespace FernleafSystems\Wordpress\Services\Core\VOs;

use FernleafSystems\Utilities\Data\Adapter\StdClassAdapter;

/**
 * Class WpHttpResponseVo
 * @see     class-wp-http-requests-response.php to_array()
 * @package FernleafSystems\Wordpress\Services\Core\VOs
 * @property string $body
 * @property string $headers
 * @property array  $response
 * @property string $cookies
 * @property string $filename
 */
class WpHttpResponseVo {

	use StdClassAdapter;

	/**
	 * @return int
	 */
	public function getCode() {
		return $this->response[ 'code' ];
	}
}