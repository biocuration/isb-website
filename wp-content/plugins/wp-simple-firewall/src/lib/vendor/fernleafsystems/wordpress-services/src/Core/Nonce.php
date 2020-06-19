<?php

namespace FernleafSystems\Wordpress\Services\Core;

/**
 * Class Nonce
 * @package FernleafSystems\Wordpress\Services\Core
 */
class Nonce {

	/**
	 * @var bool
	 */
	private $bIncludeUserId;

	/**
	 * @var string
	 */
	private $sAction;

	public function create() {
		if ( !$this->hasAction() ) {
			throw new \Exception( 'No action specified for nonce' );
		}
		return $this->isIncludeUserId() ? wp_create_nonce( $this->getAction() ) : $this->createNonceNoUser();
	}

	/**
	 * @param string $sNonce
	 * @return false|int
	 * @throws \Exception
	 */
	public function verify( $sNonce ) {
		if ( !$this->hasAction() ) {
			throw new \Exception( 'No action specified for nonce' );
		}
		return $this->isIncludeUserId() ? wp_verify_nonce( $sNonce, $this->getAction() ) : $this->verifyNonceNoUser( $sNonce );
	}

	/**
	 * Taken directly from wp_create_nonce() but excludes the user ID part.
	 * @return false|string
	 */
	private function createNonceNoUser() {
		$token = wp_get_session_token();
		$i = wp_nonce_tick();
		return substr( wp_hash( $i.'|'.$this->getAction().'|'.$token, 'nonce' ), -12, 10 );
	}

	/**
	 * @param $sNonce
	 * @return int
	 * @throws \Exception
	 */
	private function verifyNonceNoUser( $sNonce ) {
		if ( empty( $sNonce ) ) {
			throw new \Exception( 'Nonce is empty' );
		}

		$token = wp_get_session_token();
		$i = wp_nonce_tick();

		// Nonce generated 0-12 hours ago.
		$expected = substr( wp_hash( $i.'|'.$this->getAction().'|'.$token, 'nonce' ), -12, 10 );
		if ( hash_equals( $expected, $sNonce ) ) {
			return 1;
		}

		// Nonce generated 12-24 hours ago.
		$expected = substr( wp_hash( ( $i - 1 ).'|'.$this->getAction().'|'.$token, 'nonce' ), -12, 10 );
		if ( hash_equals( $expected, $sNonce ) ) {
			return 2;
		}

		throw new \Exception( 'Nonce verification failed.' );
	}

	/**
	 * @return string
	 */
	public function getAction() {
		return (string)$this->sAction;
	}

	/**
	 * @return bool
	 */
	public function hasAction() {
		return !empty( $this->sAction );
	}

	/**
	 * @return bool
	 */
	public function isIncludeUserId() {
		return isset( $this->bIncludeUserId ) ? (bool)$this->bIncludeUserId : true;
	}

	/**
	 * @param bool $sAction
	 * @return $this
	 */
	public function setAction( $sAction ) {
		$this->sAction = $sAction;
		return $this;
	}

	/**
	 * @param bool $bUse
	 * @return $this
	 */
	public function setIncludeUserId( $bUse ) {
		$this->bIncludeUserId = $bUse;
		return $this;
	}
}