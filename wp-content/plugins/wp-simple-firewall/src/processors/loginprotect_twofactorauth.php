<?php

if ( !class_exists( 'ICWP_WPSF_Processor_LoginProtect_TwoFactorAuth', false ) ):

	require_once( dirname(__FILE__).DIRECTORY_SEPARATOR.'basedb.php' );

	class ICWP_WPSF_Processor_LoginProtect_TwoFactorAuth extends ICWP_WPSF_BaseDbProcessor {

		/**
		 * @var string
		 */
		protected $nDaysToKeepLog = 1;

		/**
		 * @param ICWP_WPSF_FeatureHandler_LoginProtect $oFeatureOptions
		 */
		public function __construct( ICWP_WPSF_FeatureHandler_LoginProtect $oFeatureOptions ) {
			parent::__construct( $oFeatureOptions, $oFeatureOptions->getTwoFactorAuthTableName() );
		}

		/**
		 * Resets the object values to be re-used anew
		 */
		public function init() {
			parent::init();
			$this->setAutoExpirePeriod( DAY_IN_SECONDS * $this->nDaysToKeepLog );
		}

		/**
		 */
		public function run() {
			if ( !$this->readyToRun() ) {
				return;
			}

			if ( $this->loadDataProcessor()->FetchGet( 'wpsf-action' ) == 'linkauth' ) {
				add_action( 'init', array( $this, 'validateUserAuthLink' ), 10 );
			}

			add_action( 'show_user_profile', array( $this, 'addEmailAuthenticationOptionsToUserProfile' ) );

			// At this stage (30,3) WordPress has already (20) authenticated the user. So if the login
			// is valid, the filter will have a valid WP_User object passed to it.
			add_filter( 'authenticate', array( $this, 'setupPendingTwoFactorAuth' ), 30, 2 );
		}

		/**
		 * Checks the link details to ensure all is valid before authorizing the user.
		 */
		public function validateUserAuthLink() {
			/** @var ICWP_WPSF_FeatureHandler_LoginProtect $oFO */
			$oFO = $this->getFeatureOptions();
			$oDp = $this->loadDataProcessor();
			// authkey=%s&wpsf-action=%s&username=%s&sessionid

			if ( $oDp->FetchGet( 'authkey' ) !== $oFO->getTwoAuthSecretKey() ) {
				return;
			}

			$sUsername = $oDp->FetchGet( 'username' );
			$sSessionId = $oDp->FetchGet( 'sessionid' );

			if ( empty( $sUsername ) || empty( $sSessionId ) ) {
				return;
			}

			$oWp = $this->loadWpFunctionsProcessor();

			// First, determine if there's a pending auth for the given username + Session ID
			$aCurrentlyPending = $this->query_GetPendingAuthForUsername( $sUsername );
			if ( empty( $aCurrentlyPending ) || !is_array( $aCurrentlyPending ) || $aCurrentlyPending['session_id'] != $sSessionId ) {
				$oWp->redirectToHome();
				return;
			}

			if ( $this->setLoginAuthActive( $sSessionId, $sUsername ) ) {
				$sAuditMessage = sprintf( _wpsf__( 'User "%s" verified their identity using Two-Factor Authentication.' ), $sUsername );
				$this->addToAuditEntry( $sAuditMessage, 2, 'login_protect_two_factor_verified' );
				$this->doStatIncrement( 'login.twofactor.verified' );
				$this->loadWpUsersProcessor()->setUserLoggedIn( $sUsername );

				$sRedirectTo = esc_url( $oDp->FetchGet( 'redirect_to' ) );
				if ( !empty( $sRedirectTo ) ) {
					$oWp->doRedirect( urldecode( $sRedirectTo ) );
				}
				else {
					$oWp->redirectToAdmin();
				}
			}
			$oWp->redirectToHome();
		}

		/**
		 * If $inoUser is a valid WP_User object, then the user logged in correctly.
		 *
		 * The flow is as follows:
		 * 0. If username is empty, there was no login attempt.
		 * 1. First we determine whether the user's login credentials were valid according to WordPress ($fUserLoginSuccess)
		 * 2. Then we ask our 2-factor processor whether the current IP address + username combination is authenticated.
		 * 		a) if yes, we return the WP_User object and login proceeds as per usual.
		 * 		b) if no, we return null, which will send the message back to the user that the login details were invalid.
		 * 3. If however the user's IP address + username combination is not authenticated, we react differently. We do not want
		 * 	to give away whether a login was successful, or even the login username details exist. So:
		 * 		a) if the login was a success we add a pending record to the authentication DB for this username+IP address combination and send the appropriate verification email
		 * 		b) then, we give back a message saying that if the login was successful, they would have received a verification email. In this way we give nothing away.
		 * 		c) note at this stage, if the username was empty, we give back nothing (this happens when wp-login.php is loaded as normal.
		 *
		 * @param WP_User|WP_Error|null $oUser
		 * @param string $sUsername
		 * @return WP_Error|WP_User|null	- WP_User when the login success AND the IP is authenticated. null when login not successful but IP is valid. WP_Error otherwise.
		 */
		public function setupPendingTwoFactorAuth( $oUser, $sUsername ) {

			if ( empty( $sUsername ) || is_wp_error( $oUser ) ) {
				return $oUser;
			}

			$bUserLoginSuccess = is_object( $oUser ) && ( $oUser instanceof WP_User );

			if ( $bUserLoginSuccess ) {

				/** @var ICWP_WPSF_FeatureHandler_LoginProtect $oFO */
				$oFO = $this->getFeatureOptions();
				if ( !$oFO->getUserHasEmailAuthenticationActive( $oUser ) ) {
					return $oUser;
				}

				// Create a new 2-factor auth pending entry
				$aNewAuthData = $this->query_DoCreatePendingLoginAuth( $oUser->get( 'user_login' ) );

				// Now send email with authentication link for user.
				if ( is_array( $aNewAuthData ) ) {
					$this->doStatIncrement( 'login.twofactor.started' );
					$this->sendEmailTwoFactorVerify( $oUser, $aNewAuthData['ip'], $aNewAuthData['session_id'] );

					// We put this right at the end so as to nullify the effect of black marking on failed login (which this appears to be due to WP_Error)
					add_filter( $this->getFeatureOptions()->doPluginPrefix( 'ip_black_mark' ), '__return_false', 1000 );
				}
			}

			$sErrorString = _wpsf__( "Login is protected by 2-factor authentication." )
				.' '._wpsf__( "If your login details were correct, you will have received an email to complete the login process." ) ;
			return new WP_Error( 'wpsf_loginauth', $sErrorString );
		}

		/**
		 * @param string $sSessionId
		 * @param string $sUsername
		 * @return boolean
		 */
		public function setLoginAuthActive( $sSessionId, $sUsername ) {
			// 1. Terminate old entries
			$this->query_DoTerminateActiveLogins( $sUsername );

			// 2. Authenticate new entry
			$aWhere = array(
				'session_id'	=> $sSessionId,
				'wp_username'	=> $sUsername
			);
			return $this->query_DoMakePendingLoginAuthActive( $aWhere );
		}

		/**
		 * Note: Username should be UNESCAPED as it uses wpdb::insert()
		 *
		 * @param string $sUsername
		 * @return boolean
		 */
		protected function query_DoCreatePendingLoginAuth( $sUsername ) {

			if ( empty( $sUsername ) ) {
				return false;
			}

			// First set any other pending entries for the given user to be deleted.
			$aSetDeleted = array(
				'deleted_at'	=> $this->time(),
				'expired_at'	=> $this->time(),
			);
			$aOldPendingAuth = array(
				'pending'		=> 1,
				'deleted_at'	=> 0,
				'wp_username'	=> $sUsername
			);
			$this->updateRowsWhere( $aSetDeleted, $aOldPendingAuth );

			// Now add new pending entry
			$aNewData = array();
			$aNewData[ 'session_id' ]	= $this->getController()->getSessionId();
			$aNewData[ 'ip' ]			= $this->loadDataProcessor()->getVisitorIpAddress( true );
			$aNewData[ 'wp_username' ]	= $sUsername;
			$aNewData[ 'pending' ]		= 1;
			$aNewData[ 'created_at' ]	= $this->time();

			$mResult = $this->insertData( $aNewData );
			return $mResult ? $aNewData : $mResult;
		}

		/**
		 * Must provide "session_id" and "wp_username".
		 * Will update the authentication table so that it is active (pending=0).
		 *
		 * Note: Data should be UNESCAPED as it uses wpdb::update()
		 *
		 * @param array $aWhere - session_id, wp_username
		 * @return boolean
		 */
		protected function query_DoMakePendingLoginAuthActive( $aWhere ) {

			if ( empty( $aWhere['session_id'] ) || empty( $aWhere['wp_username'] ) ) {
				return false;
			}

			// Activate the new one.
			$aWhere['pending'] 		= 1;
			$aWhere['deleted_at']	= 0;
			$mResult = $this->updateRowsWhere( array( 'pending' => 0 ), $aWhere );
			return ( $mResult > 0 );
		}

		/**
		 * @param string $sWpUsername Specify a username to terminate only those logins
		 * @return bool|int
		 */
		protected function query_DoTerminateActiveLogins( $sWpUsername = '' ) {
			$sQuery = "
			UPDATE `%s`
			SET `deleted_at`	= '%s',
				`expired_at`	= '%s'
			WHERE
				`deleted_at`	= 0
				AND `pending`	= '0'
				%s
			";
			$sQuery = sprintf( $sQuery,
				$this->getTableName(),
				$this->time(),
				$this->time(),
				empty( $sWpUsername ) ? '' : "AND `wp_username`		= '".esc_sql( $sWpUsername )."'"
			);
			return $this->loadDbProcessor()->doSql( $sQuery );
		}

		/**
		 * @param string $sUsername
		 * @return array|null
		 */
		protected function query_GetPendingAuthForUsername( $sUsername ) {
			$sQuery = "
				SELECT *
				FROM `%s`
				WHERE
					`wp_username`		= '%s'
					AND `pending`		= '1'
					AND `deleted_at`	= 0
					AND `expired_at`	= 0
			";

			$sQuery = sprintf( $sQuery,
				$this->getTableName(),
				$sUsername
			);
			$mResult = $this->selectCustom( $sQuery );
			return ( is_array( $mResult ) && count( $mResult ) == 1 ) ? $mResult[0] : null ;
		}

		/**
		 * Given the necessary components, creates the 2-factor verification link for giving to the user.
		 *
		 * @param string $sUser
		 * @param string $sSessionId
		 * @return string
		 */
		protected function generateTwoFactorVerifyLink( $sUser, $sSessionId ) {
			$sUrl = $this->generateTwoFactorVerifyUrl( $sUser, $sSessionId );
			return sprintf( '<a href="%s" target="_blank">%s</a>', $sUrl, $sUrl );
		}

		/**
		 * @param string $sUser
		 * @param string $sSessionId
		 * @return string
		 */
		protected function generateTwoFactorVerifyUrl( $sUser, $sSessionId ) {
			/** @var ICWP_WPSF_FeatureHandler_LoginProtect $oFO */
			$oFO = $this->getFeatureOptions();
			$aQueryArgs = array(
				'authkey' 		=> $oFO->getTwoAuthSecretKey(),
				'wpsf-action'	=> 'linkauth',
				'username'		=> rawurlencode( $sUser ),
				'sessionid'		=> $sSessionId
			);
			$sRedirectTo = esc_url( $this->loadDataProcessor()->FetchPost( 'redirect_to' ) );
			if ( !empty( $sRedirectTo ) ) {
				$aQueryArgs[ 'redirect_to' ] = urlencode( $sRedirectTo );
			}
			return add_query_arg( $aQueryArgs, $this->loadWpFunctionsProcessor()->getHomeUrl() );
		}

		/**
		 * @param WP_User $oUser
		 * @param string $sIpAddress
		 * @param string $sSessionId
		 * @return boolean
		 */
		public function sendEmailTwoFactorVerify( WP_User $oUser, $sIpAddress, $sSessionId ) {

			$sEmail = $oUser->get( 'user_email' );
			$sAuthLink = $this->generateTwoFactorVerifyLink( $oUser->get( 'user_login' ), $sSessionId );

			$aMessage = array(
				_wpsf__( 'You, or someone pretending to be you, just attempted to login into your WordPress site.' ),
				_wpsf__( 'The IP Address / Cookie from which they tried to login is not currently verified.' ),
				_wpsf__('Click the following link to validate and complete the login process.').' '._wpsf__('You will be logged in automatically upon successful authentication.'),
				sprintf( _wpsf__( 'Username: %s' ), $oUser->get( 'user_login' ) ),
				sprintf( _wpsf__( 'IP Address: %s' ), $sIpAddress ),
				sprintf( _wpsf__( 'Authentication Link: %s' ), $sAuthLink )
			);
			$sEmailSubject = sprintf( _wpsf__( 'Two-Factor Login Verification for %s' ), $this->loadWpFunctionsProcessor()->getHomeUrl() );

			$bResult = $this->getEmailProcessor()->sendEmailTo( $sEmail, $sEmailSubject, $aMessage );
			if ( $bResult ) {
				$sAuditMessage = sprintf( _wpsf__('User "%s" was sent an email to verify their Identity using Two-Factor Login Auth for IP address "%s".'), $oUser->get( 'user_login' ), $sIpAddress );
				$this->addToAuditEntry( $sAuditMessage, 2, 'login_protect_two_factor_email_send' );
			}
			else {
				$sAuditMessage = sprintf( _wpsf__('Tried to send email to User "%s" to verify their identity using Two-Factor Login Auth for IP address "%s", but email sending failed.'), $oUser->get( 'user_login' ), $sIpAddress );
				$this->addToAuditEntry( $sAuditMessage, 3, 'login_protect_two_factor_email_send_fail' );
			}
			return $bResult;
		}

		/**
		 * This MUST only ever be hooked into when the User is looking at their OWN profile, so we can use "current user"
		 * functions.  Otherwise we need to be careful of mixing up users.
		 * @param WP_User $oUser
		 */
		public function addEmailAuthenticationOptionsToUserProfile( $oUser ) {
			/** @var ICWP_WPSF_FeatureHandler_LoginProtect $oFO */
			$oFO = $this->getFeatureOptions();
			$aData = array(
				'user_has_email_authentication_active' => $oFO->getUserHasEmailAuthenticationActive( $oUser ),
				'user_has_email_authentication_enforced' => $oFO->getIsUserSubjectToEmailAuthentication( $oUser ),
				'is_my_user_profile' => ( $oUser->ID == $this->loadWpUsersProcessor()->getCurrentWpUserId() ),
				'i_am_valid_admin' => $this->getController()->getIsValidAdminArea( true ),
				'user_to_edit_is_admin' => $this->loadWpUsersProcessor()->isUserAdmin( $oUser ),
				'strings' => array(
					'label_email_authentication' => _wpsf__( 'Email Authentication' ),
					'title' => _wpsf__( 'Email Authentication' ),
					'description_email_authentication_checkbox' => _wpsf__( 'Check the box to enable email-based login authentication.' ),
					'provided_by' => sprintf( _wpsf__( 'Provided by %s' ), $this->getController()->getHumanName() )
				)
			);

			$aData['bools'] = array(
				'checked' => $aData[ 'user_has_email_authentication_active' ] || $aData[ 'user_has_email_authentication_enforced' ],
				'disabled' => true || $aData[ 'user_has_email_authentication_enforced' ] //TODO: Make email authentication a per-user setting
			);

			echo $this->getFeatureOptions()->renderTemplate( 'snippets/user_profile_emailauthentication.php', $aData );
		}

		/**
		 * @return string
		 */
		public function getCreateTableSql() {
			$sSqlTables = "CREATE TABLE %s (
				id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				session_id varchar(32) NOT NULL DEFAULT '',
				wp_username varchar(255) NOT NULL DEFAULT '',
				ip varchar(40) NOT NULL DEFAULT '',
				pending tinyint(1) NOT NULL DEFAULT 0,
				created_at int(15) UNSIGNED NOT NULL DEFAULT 0,
				deleted_at int(15) UNSIGNED NOT NULL DEFAULT 0,
				expired_at int(15) UNSIGNED NOT NULL DEFAULT 0,
				PRIMARY KEY  (id)
			) %s;";
			return sprintf( $sSqlTables, $this->getTableName(), $this->loadDbProcessor()->getCharCollate() );
		}

		/**
		 * @return array
		 */
		protected function getTableColumnsByDefinition() {
			$aDef = $this->getFeatureOptions()->getDefinition( 'two_factor_auth_table_columns' );
			return ( is_array( $aDef ) ? $aDef : array() );
		}

		/**
		 * @param int $nTimeStamp
		 * @return bool|int
		 */
		protected function deleteAllRowsOlderThan( $nTimeStamp ) {
			$sQuery = "
				DELETE from `%s`
				WHERE
					`created_at`		< '%s'
					AND `pending`		= '1'
			";
			$sQuery = sprintf( $sQuery,
				$this->getTableName(),
				esc_sql( $nTimeStamp )
			);
			return $this->loadDbProcessor()->doSql( $sQuery );
		}
	}
endif;
