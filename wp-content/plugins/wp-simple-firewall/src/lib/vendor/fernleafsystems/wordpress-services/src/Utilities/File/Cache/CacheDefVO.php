<?php

namespace FernleafSystems\Wordpress\Services\Utilities\File\Cache;

/**
 * Class CacheDefVO
 * @package FernleafSystems\Wordpress\Services\Utilities\File\Cache
 * @property string $dir
 * @property string $file_fragment
 * @property string $expiration
 * @property bool   $touch_on_load
 * @property array  $data
 */
class CacheDefVO {

	use \FernleafSystems\Utilities\Data\Adapter\StdClassAdapter;
}