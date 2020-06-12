<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\LoginGuard\Lib\AntiBot\FormProviders;

class MemberPress extends BaseFormProvider {

	protected function login() {
		add_action( 'mepr-login-form-before-submit', [ $this, 'formInsertsPrint' ], 100 );
		add_filter( 'mepr-validate-login', [ $this, 'checkLogin' ], 100 );
		/**
		 * We have to add a checkbox to the password reset form because MemberPress attempts to
		 * login the given user upon success of password update. Without this checkbox being present
		 * the login will fail (though the password update will not).
		 */
		add_action( 'mepr-reset-password-after-password-fields', [ $this, 'formInsertsPrint' ], 100 );
	}

	protected function register() {
		add_action( 'mepr-checkout-before-submit', [ $this, 'formInsertsPrint' ], 10 );
		add_filter( 'mepr-validate-signup', [ $this, 'checkRegister' ], 10, 2 );
	}

	protected function lostpassword() {
		add_action( 'mepr-forgot-password-form', [ $this, 'formInsertsPrint' ], 100 );
		add_filter( 'mepr-validate-forgot-password', [ $this, 'checkLostPassword' ], 100 );
	}

	/**
	 * @param array $aErrors
	 * @return array
	 */
	public function checkLogin( $aErrors ) {
		if ( !empty( $aErrors ) ) {
			try {
				$this->setActionToAudit( 'memberpress-login' )
					 ->checkProviders();
			}
			catch ( \Exception $oE ) {
				$aErrors[] = $oE->getMessage();
			}
		}
		return $aErrors;
	}

	/**
	 * @param array $aErrors
	 * @return array
	 */
	public function checkLostPassword( $aErrors ) {
		error_log( __FUNCTION__ );
		if ( !empty( $aErrors ) ) {
			try {
				$this->setActionToAudit( 'memberpress-lostpassword' )
					 ->checkProviders();
			}
			catch ( \Exception $oE ) {
				$aErrors[] = $oE->getMessage();
			}
		}
		return $aErrors;
	}

	/**
	 * Errors are passed about here using an array of strings.
	 * @param string[] $aErrors
	 * @return string[]
	 */
	public function checkRegister( $aErrors ) {
		if ( !empty( $aErrors ) ) {
			try {
				$this->setActionToAudit( 'memberpress-register' )
					 ->checkProviders();
			}
			catch ( \Exception $oE ) {
				$aErrors[] = $oE->getMessage();
			}
		}
		return $aErrors;
	}
}