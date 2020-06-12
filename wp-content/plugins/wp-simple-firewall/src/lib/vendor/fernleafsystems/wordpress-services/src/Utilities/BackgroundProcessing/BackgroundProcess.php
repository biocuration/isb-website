<?php

namespace FernleafSystems\Wordpress\Services\Utilities\BackgroundProcessing;

use FernleafSystems\Wordpress\Services\Services;

/**
 * Class BackgroundProcess
 * @package FernleafSystems\Wordpress\Services\Utilities\BackgroundProcessing
 */
abstract class BackgroundProcess extends \WP_Background_Process {

	/**
	 * @var int
	 */
	private $nExpirationInterval;

	/**
	 * Expired Cron_hook_identifier
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $expired_cron_hook_identifier;

	/**
	 * @param string $sAction
	 * @param string $sPrefix
	 */
	public function __construct( $sAction = '', $sPrefix = 'apto' ) {
		$this->setPrefix( $sPrefix )
			 ->setAction( $sAction );

		parent::__construct();

		$this->expired_cron_hook_identifier = $this->identifier.'_expired_cron';
		add_action( $this->expired_cron_hook_identifier, [ $this, 'handleExpiredItems' ] );
	}

	/**
	 * Dispatch
	 *
	 * @access public
	 * @return void
	 */
	public function dispatch() {
		// A cron that automatically cleans up expired items
		$this->scheduleExpiredCleanup();

		return parent::dispatch();
	}

	/**
	 * Overrides base to simply 'return' instead of exit() this healthcheck
	 */
	public function handle_cron_healthcheck() {
		if ( $this->is_process_running() ) {
			// Background process already running.
			return;
		}

		if ( $this->is_queue_empty() ) {
			// No data to process.
			$this->clear_scheduled_event();
			return;
		}

		$this->handle();
	}

	/**
	 * By default the full data of the processing item is post'ed in the request. We don't need/want that.
	 * @return array
	 */
	protected function get_post_args() {
		$aArgs = parent::get_post_args();

		if ( isset( $aArgs[ 'body' ] ) ) {
			$aArgs[ 'body' ] = '';
		}

		return $aArgs;
	}

	protected function scheduleExpiredCleanup() {
		$nExpiration = $this->getExpirationInterval();
		if ( $nExpiration > 0 ) {

			function_exists( 'wp_unschedule_hook' ) ?
				wp_unschedule_hook( $this->expired_cron_hook_identifier )
				: wp_clear_scheduled_hook( $this->expired_cron_hook_identifier );

			if ( !wp_next_scheduled( $this->expired_cron_hook_identifier ) ) {
				wp_schedule_single_event(
					Services::Request()->carbon()->addSeconds( $nExpiration )->timestamp,
					$this->expired_cron_hook_identifier
				);
			}
		}
	}

	/**
	 */
	public function handleExpiredItems() {
		// override to handle expired items according to Expiration Interval
	}

	/**
	 * @return int
	 */
	public function getExpirationInterval() {
		return (int)$this->nExpirationInterval;
	}

	/**
	 * @param int $sAction
	 * @return $this
	 */
	public function setAction( $sAction ) {
		if ( !empty( $sAction ) ) {
			$this->action = $sAction;
		}
		return $this;
	}

	/**
	 * @param int $nExpirationInterval - seconds
	 * @return $this
	 */
	public function setExpirationInterval( $nExpirationInterval ) {
		$this->nExpirationInterval = $nExpirationInterval;
		return $this;
	}

	/**
	 * @param int $sPrefix
	 * @return $this
	 */
	public function setPrefix( $sPrefix ) {
		if ( !empty( $sPrefix ) ) {
			$this->prefix = $sPrefix;
		}
		return $this;
	}
}