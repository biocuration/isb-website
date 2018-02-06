<?php
if ( class_exists( 'ICWP_UserMeta', false ) ) {
	return;
}

/**
 * @property string $email_secret
 * @property bool   $email_validated
 * @property string $ga_secret
 * @property bool   $ga_validated
 * @property array  $hash_loginmfa
 * @property string $yubi_secret
 * @property bool   $yubi_validated
 * @property string $code_tfaemail
 * @property int    $last_login_at
 * @property int    $login_intent_expires_at
 * @property string $prefix
 * @property int    $user_id
 * Class ICWP_UserMeta
 */
class ICWP_UserMeta extends ICWP_WPSF_Foundation {

	/**
	 * @var array
	 */
	protected $aData;

	/**
	 * @param string $sPrefix
	 * @param int    $nUserId
	 */
	public function __construct( $sPrefix, $nUserId = 0 ) {
		$this->load( $sPrefix, $nUserId );
	}

	/**
	 */
	public function __destruct() {
		$this->save();
	}

	/**
	 * Cannot use Data store (__get()) yet
	 * @param int $sPrefix
	 * @param int $nUserId
	 * @return $this
	 */
	private function load( $sPrefix, $nUserId ) {
		$aStore = $this->loadWpUsers()->getUserMeta( $sPrefix.'-meta', $nUserId );
		if ( !is_array( $aStore ) ) {
			$aStore = array();
		}
		$this->aData = $aStore;
		$this->prefix = $sPrefix;
		$this->user_id = $nUserId;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function delete() {
		if ( $this->user_id > 0 ) {
			$this->loadWpUsers()->deleteUserMeta( $this->getStorageKey(), $this->user_id );
		}
		return $this;
	}

	/**
	 * @return $this
	 */
	public function save() {
		if ( $this->user_id > 0 ) {
			$this->loadWpUsers()->updateUserMeta( $this->getStorageKey(), $this->aData, $this->user_id );
		}
		return $this;
	}

	/**
	 * @param string $sKey
	 * @return mixed|null
	 */
	public function __get( $sKey ) {
		return isset( $this->aData[ $sKey ] ) ? $this->aData[ $sKey ] : null;
	}

	/**
	 * @param string $sKey
	 * @return bool
	 */
	public function __isset( $sKey ) {
		return isset( $this->aData[ $sKey ] );
	}

	/**
	 * @param string $sKey
	 * @param        $mValue
	 */
	public function __set( $sKey, $mValue ) {
		$this->aData[ $sKey ] = $mValue;
		$this->save();
	}

	/**
	 * @param string $sKey
	 */
	public function __unset( $sKey ) {
		unset( $this->aData[ $sKey ] );
		$this->save();
	}

	/**
	 * @return string
	 */
	private function getStorageKey() {
		return $this->prefix.'-meta';
	}
}