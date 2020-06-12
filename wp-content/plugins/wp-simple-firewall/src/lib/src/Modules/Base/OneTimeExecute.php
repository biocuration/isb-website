<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\Base;

trait OneTimeExecute {

	private $bExecuted = false;

	/**
	 * @return bool
	 */
	protected function canRun() {
		return true;
	}

	public function execute() {
		if ( !$this->isAlreadyExecuted() && $this->canRun() ) {
			$this->bExecuted = true;
			$this->run();
		}
	}

	/**
	 * @return bool
	 */
	protected function isAlreadyExecuted() {
		return (bool)$this->bExecuted;
	}

	protected function run() {
	}
}