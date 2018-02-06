<?php

if ( class_exists( 'ICWP_WPSF_Processor_LoginProtect_TwoFactorAuth', false ) ) {
	return;
}

require_once( dirname( __FILE__ ).DIRECTORY_SEPARATOR.'loginprotect_intent_base.php' );

class ICWP_WPSF_Processor_LoginProtect_TwoFactorAuth extends ICWP_WPSF_Processor_LoginProtect_IntentBase {

	public function run() {
		parent::run();
	}

	/**
	 * @param WP_User|WP_Error|null $oUser
	 * @return WP_Error|WP_User|null    - WP_User when the login success AND the IP is authenticated. null when login
	 *                                  not successful but IP is valid. WP_Error otherwise.
	 */
	public function processLoginAttempt_Filter( $oUser ) {
		/** @var ICWP_WPSF_FeatureHandler_LoginProtect $oFO */
		$oFO = $this->getFeature();

		$bLoginSuccess = is_object( $oUser ) && ( $oUser instanceof WP_User );
		if ( $bLoginSuccess && $this->hasValidatedProfile( $oUser ) && !$oFO->canUserMfaSkip( $oUser ) ) {

			// Now send email with authentication link for user.
			$this->doStatIncrement( 'login.twofactor.started' );

			$oMeta = $this->loadWpUsers()->metaVoForUser( $this->prefix(), $oUser->ID );
			$oMeta->code_tfaemail = $this->getSessionHashCode();

			$this->sendEmailTwoFactorVerify( $oUser );
		}
		return $oUser;
	}

	/**
	 * @param bool $bIsSuccess
	 */
	protected function auditLogin( $bIsSuccess ) {
		if ( $bIsSuccess ) {
			$this->addToAuditEntry(
				sprintf(
					_wpsf__( 'User "%s" verified their identity using Email Two-Factor Authentication.' ),
					$this->loadWpUsers()->getCurrentWpUser()->get( 'user_login' )
				),
				2, 'login_protect_two_factor_verified'
			);
			$this->doStatIncrement( 'login.twofactor.verified' );
		}
		else {
			$this->addToAuditEntry(
				sprintf(
					_wpsf__( 'User "%s" failed to verify their identity using Email Two-Factor Authentication.' ),
					$this->loadWpUsers()->getCurrentWpUser()->get( 'user_login' )
				),
				2, 'login_protect_two_factor_failed'
			);
			$this->doStatIncrement( 'login.twofactor.failed' );
		}
	}

	/**
	 * @param WP_User $oUser
	 * @param string  $sOtpCode
	 * @return bool
	 */
	protected function processOtp( $oUser, $sOtpCode ) {
		$bValid = ( $sOtpCode == $this->getStoredSessionHashCode() );
		if ( $bValid ) {
			unset( $this->loadWpUsers()->metaVoForUser( $this->prefix() )->code_tfaemail );
		}
		return $bValid;
	}

	/**
	 * @param array $aFields
	 * @return array
	 */
	public function addLoginIntentField( $aFields ) {
		if ( $this->getCurrentUserHasValidatedProfile() ) {
			$aFields[] = array(
				'name'        => $this->getLoginFormParameter(),
				'type'        => 'text',
				'value'       => $this->fetchCodeFromRequest(),
				'placeholder' => _wpsf__( 'This code was just sent to your registered Email address.' ),
				'text'        => _wpsf__( 'Email OTP' ),
				'help_link'   => 'http://icwp.io/3t'
			);
		}
		return $aFields;
	}

	/**
	 * @param WP_User $oUser
	 * @return bool
	 */
	public function hasValidatedProfile( $oUser ) {
		/** @var ICWP_WPSF_FeatureHandler_LoginProtect $oFO */
		$oFO = $this->getFeature();
		// Currently it's a global setting but this will evolve to be like Google Authenticator so that it's a user meta
		return ( $oFO->getIsEmailAuthenticationEnabled() && $this->getIsUserSubjectToEmailAuthentication( $oUser ) );
	}

	/**
	 * TODO: http://stackoverflow.com/questions/3499104/how-to-know-the-role-of-current-user-in-wordpress
	 * @param WP_User $oUser
	 * @return bool
	 */
	public function getIsUserSubjectToEmailAuthentication( $oUser ) {
		$nUserLevel = $oUser->get( 'user_level' );

		$aSubjectedUserLevels = $this->getFeature()->getOpt( 'two_factor_auth_user_roles' );
		if ( empty( $aSubjectedUserLevels ) || !is_array( $aSubjectedUserLevels ) ) {
			$aSubjectedUserLevels = array( 1, 2, 3, 8 ); // by default all roles except subscribers!
		}

		// see: https://codex.wordpress.org/Roles_and_Capabilities#User_Level_to_Role_Conversion

		// authors, contributors and subscribers
		if ( $nUserLevel < 3 && in_array( $nUserLevel, $aSubjectedUserLevels ) ) {
			return true;
		}
		// editors
		if ( $nUserLevel >= 3 && $nUserLevel < 8 && in_array( 3, $aSubjectedUserLevels ) ) {
			return true;
		}
		// administrators
		if ( $nUserLevel >= 8 && $nUserLevel <= 10 && in_array( 8, $aSubjectedUserLevels ) ) {
			return true;
		}
		return false;
	}

	/**
	 * @return string
	 */
	protected function genSessionHash() {
		/** @var ICWP_WPSF_FeatureHandler_LoginProtect $oFO */
		$oFO = $this->getFeature();
		return hash_hmac(
			'sha1',
			$this->getController()->getUniqueRequestId(),
			$oFO->getTwoAuthSecretKey()
		);
	}

	/**
	 * @return string The unique 2FA 6-digit code
	 */
	protected function getSessionHashCode() {
		return strtoupper( substr( $this->genSessionHash(), 0, 6 ) );
	}

	/**
	 * @return string The unique 2FA 6-digit code
	 */
	protected function getStoredSessionHashCode() {
		return $this->getCurrentUserMeta()->code_tfaemail;
	}

	/**
	 * @param WP_User $oUser
	 * @return boolean
	 */
	protected function sendEmailTwoFactorVerify( WP_User $oUser ) {
		$sIpAddress = $this->ip();
		$sEmail = $oUser->get( 'user_email' );

		$aMessage = array(
			_wpsf__( 'Someone just attempted to login into your WordPress site.' ),
			_wpsf__( 'Login for this user account requires verification.' ),
			'',
			sprintf( '<strong>%s</strong>', _wpsf__( 'Login Details' ) ),
			sprintf( _wpsf__( 'Username: %s' ), $oUser->get( 'user_login' ) ),
			sprintf( _wpsf__( 'IP Address: %s' ), $sIpAddress ),
			'',
			_wpsf__( 'Use the following code on the Login Verification page.' ),
			sprintf( _wpsf__( 'Verification Code: %s' ), sprintf( '<strong>%s</strong>', $this->getSessionHashCode() ) ),
			'',
			sprintf( '(<a href="%s" target="_blank">%s</a>)', 'http://icwp.io/96', _wpsf__( 'Why no login link?' ) ),
			''
		);
		$sEmailSubject = sprintf( _wpsf__( '[%s] Two-Factor Login Verification' ), $this->loadWp()->getSiteName() );

		$bResult = $this->getEmailProcessor()
						->sendEmailTo( $sEmail, $sEmailSubject, $aMessage );
		if ( $bResult ) {
			$sAuditMessage = sprintf( _wpsf__( 'User "%s" was sent an email to verify their Identity using Two-Factor Login Auth for IP address "%s".' ), $oUser->get( 'user_login' ), $sIpAddress );
			$this->addToAuditEntry( $sAuditMessage, 2, 'login_protect_two_factor_email_send' );
		}
		else {
			$sAuditMessage = sprintf( _wpsf__( 'Tried to send email to User "%s" to verify their identity using Two-Factor Login Auth for IP address "%s", but email sending failed.' ), $oUser->get( 'user_login' ), $sIpAddress );
			$this->addToAuditEntry( $sAuditMessage, 3, 'login_protect_two_factor_email_send_fail' );
		}
		return $bResult;
	}

	/**
	 * This MUST only ever be hooked into when the User is looking at their OWN profile, so we can use "current user"
	 * functions.  Otherwise we need to be careful of mixing up users.
	 * @param WP_User $oUser
	 */
	public function addOptionsToUserProfile( $oUser ) {
		$oWp = $this->loadWpUsers();
		$bValidatedProfile = $this->hasValidatedProfile( $oUser );
		$aData = array(
			'user_has_email_authentication_active'   => $bValidatedProfile,
			'user_has_email_authentication_enforced' => $this->getIsUserSubjectToEmailAuthentication( $oUser ),
			'is_my_user_profile'                     => ( $oUser->ID == $oWp->getCurrentWpUserId() ),
			'i_am_valid_admin'                       => $this->getController()->getIsValidAdminArea( true ),
			'user_to_edit_is_admin'                  => $oWp->isUserAdmin( $oUser ),
			'strings'                                => array(
				'label_email_authentication'                => _wpsf__( 'Email Authentication' ),
				'title'                                     => _wpsf__( 'Email Authentication' ),
				'description_email_authentication_checkbox' => _wpsf__( 'Check the box to enable email-based login authentication.' ),
				'provided_by'                               => sprintf( _wpsf__( 'Provided by %s' ), $this->getController()
																										  ->getHumanName() )
			)
		);

		$aData[ 'bools' ] = array(
			'checked'  => $bValidatedProfile || $aData[ 'user_has_email_authentication_enforced' ],
			'disabled' => true || $aData[ 'user_has_email_authentication_enforced' ]
			//TODO: Make email authentication a per-user setting
		);

		echo $this->getFeature()->renderTemplate( 'snippets/user_profile_emailauthentication.php', $aData );
	}

	/**
	 * @return string
	 */
	protected function getStub() {
		return ICWP_WPSF_Processor_LoginProtect_Track::Factor_Email;
	}

	/**
	 * @return string
	 */
	protected function get2FaCodeUserMetaKey() {
		return $this->getFeature()->prefix( 'tfaemail_reqid' );
	}
}