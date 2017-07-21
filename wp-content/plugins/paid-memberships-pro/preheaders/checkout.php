<?php
global $post, $gateway, $wpdb, $besecure, $discount_code, $discount_code_id, $pmpro_level, $pmpro_levels, $pmpro_msg, $pmpro_msgt, $pmpro_review, $skip_account_fields, $pmpro_paypal_token, $pmpro_show_discount_code, $pmpro_error_fields, $pmpro_required_billing_fields, $pmpro_required_user_fields, $wp_version, $current_user;

//make sure we know current user's membership level
if ( $current_user->ID ) {
	$current_user->membership_level = pmpro_getMembershipLevelForUser( $current_user->ID );
}

//this var stores fields with errors so we can make them red on the frontend
$pmpro_error_fields = array();

//blank array for required fields, set below
$pmpro_required_billing_fields = array();
$pmpro_required_user_fields    = array();

//was a gateway passed?
if ( ! empty( $_REQUEST['gateway'] ) ) {
	$gateway = $_REQUEST['gateway'];
} elseif ( ! empty( $_REQUEST['review'] ) ) {
	$gateway = "paypalexpress";
} else {
	$gateway = pmpro_getOption( "gateway" );
}

//set valid gateways - the active gateway in the settings and any gateway added through the filter will be allowed
if ( pmpro_getOption( "gateway", true ) == "paypal" ) {
	$valid_gateways = apply_filters( "pmpro_valid_gateways", array( "paypal", "paypalexpress" ) );
} else {
	$valid_gateways = apply_filters( "pmpro_valid_gateways", array( pmpro_getOption( "gateway", true ) ) );
}

//let's add an error now, if an invalid gateway is set
if ( ! in_array( $gateway, $valid_gateways ) ) {
	$pmpro_msg  = __( "Invalid gateway.", 'pmpro' );
	$pmpro_msgt = "pmpro_error";
}

//what level are they purchasing? (discount code passed)
$pmpro_level = pmpro_getLevelAtCheckout();

if ( empty( $pmpro_level->id ) ) {
	wp_redirect( pmpro_url( "levels" ) );
	exit( 0 );
}

//enqueue some scripts
wp_enqueue_script( 'jquery.creditCardValidator', plugins_url( '/js/jquery.creditCardValidator.js', dirname( __FILE__ ) ), array( 'jquery' ) );

global $wpdb, $current_user, $pmpro_requirebilling;
//unless we're submitting a form, let's try to figure out if https should be used

if ( ! pmpro_isLevelFree( $pmpro_level ) ) {
	//require billing and ssl
	$pagetitle            = __( "Checkout: Payment Information", 'pmpro' );
	$pmpro_requirebilling = true;
	$besecure             = pmpro_getOption( "use_ssl" );
} else {
	//no payment so we don't need ssl
	$pagetitle            = __( "Set Up Your Account", 'pmpro' );
	$pmpro_requirebilling = false;
	$besecure             = false;
}

//in case a discount code was used or something else made the level free, but we're already over ssl
if ( ! $besecure && ! empty( $_REQUEST['submit-checkout'] ) && is_ssl() ) {
	$besecure = true;
}    //be secure anyway since we're already checking out

//action to run extra code for gateways/etc
do_action( 'pmpro_checkout_preheader' );

//get all levels in case we need them
global $pmpro_levels;
$pmpro_levels = pmpro_getAllLevels();

//should we show the discount code field?
if ( $wpdb->get_var( "SELECT id FROM $wpdb->pmpro_discount_codes LIMIT 1" ) ) {
	$pmpro_show_discount_code = true;
} else {
	$pmpro_show_discount_code = false;
}
$pmpro_show_discount_code = apply_filters( "pmpro_show_discount_code", $pmpro_show_discount_code );

//by default we show the account fields if the user isn't logged in
if ( $current_user->ID ) {
	$skip_account_fields = true;
} else {
	$skip_account_fields = false;
}
//in case people want to have an account created automatically
$skip_account_fields = apply_filters( "pmpro_skip_account_fields", $skip_account_fields, $current_user );

//some options
global $tospage;
$tospage = pmpro_getOption( "tospage" );
if ( $tospage ) {
	$tospage = get_post( $tospage );
}

//load em up (other fields)
global $username, $password, $password2, $bfirstname, $blastname, $baddress1, $baddress2, $bcity, $bstate, $bzipcode, $bcountry, $bphone, $bemail, $bconfirmemail, $CardType, $AccountNumber, $ExpirationMonth, $ExpirationYear;

if ( isset( $_REQUEST['order_id'] ) ) {
	$order_id = intval( $_REQUEST['order_id'] );
} else {
	$order_id = "";
}
if ( isset( $_REQUEST['bfirstname'] ) ) {
	$bfirstname = sanitize_text_field( stripslashes( $_REQUEST['bfirstname'] ) );
} else {
	$bfirstname = "";
}
if ( isset( $_REQUEST['blastname'] ) ) {
	$blastname = sanitize_text_field( stripslashes( $_REQUEST['blastname'] ) );
} else {
	$blastname = "";
}
if ( isset( $_REQUEST['fullname'] ) ) {
	$fullname = $_REQUEST['fullname'];
}        //honeypot for spammers
if ( isset( $_REQUEST['baddress1'] ) ) {
	$baddress1 = sanitize_text_field( stripslashes( $_REQUEST['baddress1'] ) );
} else {
	$baddress1 = "";
}
if ( isset( $_REQUEST['baddress2'] ) ) {
	$baddress2 = sanitize_text_field( stripslashes( $_REQUEST['baddress2'] ) );
} else {
	$baddress2 = "";
}
if ( isset( $_REQUEST['bcity'] ) ) {
	$bcity = sanitize_text_field( stripslashes( $_REQUEST['bcity'] ) );
} else {
	$bcity = "";
}

if ( isset( $_REQUEST['bstate'] ) ) {
	$bstate = sanitize_text_field( stripslashes( $_REQUEST['bstate'] ) );
} else {
	$bstate = "";
}

//convert long state names to abbreviations
if ( ! empty( $bstate ) ) {
	global $pmpro_states;
	foreach ( $pmpro_states as $abbr => $state ) {
		if ( $bstate == $state ) {
			$bstate = $abbr;
			break;
		}
	}
}

if ( isset( $_REQUEST['bzipcode'] ) ) {
	$bzipcode = sanitize_text_field( stripslashes( $_REQUEST['bzipcode'] ) );
} else {
	$bzipcode = "";
}
if ( isset( $_REQUEST['bcountry'] ) ) {
	$bcountry = sanitize_text_field( stripslashes( $_REQUEST['bcountry'] ) );
} else {
	$bcountry = "";
}
if ( isset( $_REQUEST['bphone'] ) ) {
	$bphone = sanitize_text_field( stripslashes( $_REQUEST['bphone'] ) );
} else {
	$bphone = "";
}
if ( isset ( $_REQUEST['bemail'] ) ) {
	$bemail = sanitize_email( stripslashes( $_REQUEST['bemail'] ) );
} elseif ( is_user_logged_in() ) {
	$bemail = $current_user->user_email;
} else {
	$bemail = "";
}
if ( isset( $_REQUEST['bconfirmemail_copy'] ) ) {
	$bconfirmemail = $bemail;
} elseif ( isset( $_REQUEST['bconfirmemail'] ) ) {
	$bconfirmemail = sanitize_email( stripslashes( $_REQUEST['bconfirmemail'] ) );
} elseif ( is_user_logged_in() ) {
	$bconfirmemail = $current_user->user_email;
} else {
	$bconfirmemail = "";
}

if ( isset( $_REQUEST['CardType'] ) && ! empty( $_REQUEST['AccountNumber'] ) ) {
	$CardType = sanitize_text_field( $_REQUEST['CardType'] );
} else {
	$CardType = "";
}
if ( isset( $_REQUEST['AccountNumber'] ) ) {
	$AccountNumber = sanitize_text_field( $_REQUEST['AccountNumber'] );
} else {
	$AccountNumber = "";
}

if ( isset( $_REQUEST['ExpirationMonth'] ) ) {
	$ExpirationMonth = sanitize_text_field( $_REQUEST['ExpirationMonth'] );
} else {
	$ExpirationMonth = "";
}
if ( isset( $_REQUEST['ExpirationYear'] ) ) {
	$ExpirationYear = sanitize_text_field( $_REQUEST['ExpirationYear'] );
} else {
	$ExpirationYear = "";
}
if ( isset( $_REQUEST['CVV'] ) ) {
	$CVV = sanitize_text_field( $_REQUEST['CVV'] );
} else {
	$CVV = "";
}

if ( isset( $_REQUEST['discount_code'] ) ) {
	$discount_code = preg_replace( "/[^A-Za-z0-9\-]/", "", $_REQUEST['discount_code'] );
} else {
	$discount_code = "";
}
if ( isset( $_REQUEST['username'] ) ) {
	$username = sanitize_user( $_REQUEST['username'] );
} else {
	$username = "";
}
if ( isset( $_REQUEST['password'] ) ) {
	$password = $_REQUEST['password'];
} else {
	$password = "";
}
if ( isset( $_REQUEST['password2_copy'] ) ) {
	$password2 = $password;
} elseif ( isset( $_REQUEST['password2'] ) ) {
	$password2 = $_REQUEST['password2'];
} else {
	$password2 = "";
}
if ( isset( $_REQUEST['tos'] ) ) {
	$tos = intval( $_REQUEST['tos'] );
} else {
	$tos = "";
}

//_x stuff in case they clicked on the image button with their mouse
if ( isset( $_REQUEST['submit-checkout'] ) ) {
	$submit = $_REQUEST['submit-checkout'];
}
if ( empty( $submit ) && isset( $_REQUEST['submit-checkout_x'] ) ) {
	$submit = $_REQUEST['submit-checkout_x'];
}
if ( isset( $submit ) && $submit === "0" ) {
	$submit = true;
} elseif ( ! isset( $submit ) ) {
	$submit = false;
}

//require fields
$pmpro_required_billing_fields = array(
	"bfirstname"      => $bfirstname,
	"blastname"       => $blastname,
	"baddress1"       => $baddress1,
	"bcity"           => $bcity,
	"bstate"          => $bstate,
	"bzipcode"        => $bzipcode,
	"bphone"          => $bphone,
	"bemail"          => $bemail,
	"bcountry"        => $bcountry,
	"CardType"        => $CardType,
	"AccountNumber"   => $AccountNumber,
	"ExpirationMonth" => $ExpirationMonth,
	"ExpirationYear"  => $ExpirationYear,
	"CVV"             => $CVV
);
$pmpro_required_billing_fields = apply_filters( "pmpro_required_billing_fields", $pmpro_required_billing_fields );
$pmpro_required_user_fields    = array(
	"username"      => $username,
	"password"      => $password,
	"password2"     => $password2,
	"bemail"        => $bemail,
	"bconfirmemail" => $bconfirmemail
);
$pmpro_required_user_fields    = apply_filters( "pmpro_required_user_fields", $pmpro_required_user_fields );

//pmpro_confirmed is set to true later if payment goes through
$pmpro_confirmed = false;

//check their fields if they clicked continue
if ( $submit && $pmpro_msgt != "pmpro_error" ) {

	//make sure javascript is ok
	if ( apply_filters( "pmpro_require_javascript_for_checkout", true ) && ! empty( $_REQUEST['checkjavascript'] ) && empty( $_REQUEST['javascriptok'] ) ) {
		pmpro_setMessage( __( "There are JavaScript errors on the page. Please contact the webmaster.", "pmpro" ), "pmpro_error" );
	}

	//if we're skipping the account fields and there is no user, we need to create a username and password
	if ( $skip_account_fields && ! $current_user->ID ) {
		$username = pmpro_generateUsername( $bfirstname, $blastname, $bemail );
		if ( empty( $username ) ) {
			$username = pmpro_getDiscountCode();
		}
		$password  = pmpro_getDiscountCode() . pmpro_getDiscountCode();    //using two random discount codes
		$password2 = $password;
	}

	//check billing fields
	if ( $pmpro_requirebilling ) {
		//filter
		foreach ( $pmpro_required_billing_fields as $key => $field ) {
			if ( ! $field ) {
				$pmpro_error_fields[] = $key;
			}
		}
	}

	//check user fields
	if ( empty( $current_user->ID ) ) {
		foreach ( $pmpro_required_user_fields as $key => $field ) {
			if ( ! $field ) {
				$pmpro_error_fields[] = $key;
			}
		}
	}

	if ( ! empty( $pmpro_error_fields ) ) {
		pmpro_setMessage( __( "Please complete all required fields.", "pmpro" ), "pmpro_error" );
	}
	if ( ! empty( $password ) && $password != $password2 ) {
		pmpro_setMessage( __( "Your passwords do not match. Please try again.", "pmpro" ), "pmpro_error" );
		$pmpro_error_fields[] = "password";
		$pmpro_error_fields[] = "password2";
	}
	if ( ! empty( $bemail ) && $bemail != $bconfirmemail ) {
		pmpro_setMessage( __( "Your email addresses do not match. Please try again.", "pmpro" ), "pmpro_error" );
		$pmpro_error_fields[] = "bemail";
		$pmpro_error_fields[] = "bconfirmemail";
	}
	if ( ! empty( $bemail ) && ! is_email( $bemail ) ) {
		pmpro_setMessage( __( "The email address entered is in an invalid format. Please try again.", "pmpro" ), "pmpro_error" );
		$pmpro_error_fields[] = "bemail";
		$pmpro_error_fields[] = "bconfirmemail";
	}
	if ( ! empty( $tospage ) && empty( $tos ) ) {
		pmpro_setMessage( sprintf( __( "Please check the box to agree to the %s.", "pmpro" ), $tospage->post_title ), "pmpro_error" );
		$pmpro_error_fields[] = "tospage";
	}
	if ( ! in_array( $gateway, $valid_gateways ) ) {
		pmpro_setMessage( __( "Invalid gateway.", "pmpro" ), "pmpro_error" );
	}
	if ( ! empty( $fullname ) ) {
		pmpro_setMessage( __( "Are you a spammer?", "pmpro" ), "pmpro_error" );
	}

	if ( $pmpro_msgt == "pmpro_error" ) {
		$pmpro_continue_registration = false;
	} else {
		$pmpro_continue_registration = true;
	}
	$pmpro_continue_registration = apply_filters( "pmpro_registration_checks", $pmpro_continue_registration );

	if ( $pmpro_continue_registration ) {
		//if creating a new user, check that the email and username are available
		if ( empty( $current_user->ID ) ) {
			$ouser      = get_user_by( 'login', $username );
			$oldem_user = get_user_by( 'email', $bemail );

			//this hook can be used to allow multiple accounts with the same email address
			$oldemail = apply_filters( "pmpro_checkout_oldemail", ( false !== $oldem_user ? $oldem_user->user_email : null ) );
		}

		if ( ! empty( $ouser->user_login ) ) {
			pmpro_setMessage( __( "That username is already taken. Please try another.", "pmpro" ), "pmpro_error" );
			$pmpro_error_fields[] = "username";
		}

		if ( ! empty( $oldemail ) ) {
			pmpro_setMessage( __( "That email address is already taken. Please try another.", "pmpro" ), "pmpro_error" );
			$pmpro_error_fields[] = "bemail";
			$pmpro_error_fields[] = "bconfirmemail";
		}

		//only continue if there are no other errors yet
		if ( $pmpro_msgt != "pmpro_error" ) {
			//check recaptcha first
			global $recaptcha;
			if ( ! $skip_account_fields && ( $recaptcha == 2 || ( $recaptcha == 1 && pmpro_isLevelFree( $pmpro_level ) ) ) ) {
				global $recaptcha_privatekey;

				if ( isset( $_POST["recaptcha_challenge_field"] ) ) {
					//using older recaptcha lib
					$resp = recaptcha_check_answer( $recaptcha_privatekey,
						$_SERVER["REMOTE_ADDR"],
						$_POST["recaptcha_challenge_field"],
						$_POST["recaptcha_response_field"] );

					$recaptcha_valid  = $resp->is_valid;
					$recaptcha_errors = $resp->error;
				} else {
					//using newer recaptcha lib
					$reCaptcha = new pmpro_ReCaptcha( $recaptcha_privatekey );
					$resp      = $reCaptcha->verifyResponse( $_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"] );

					$recaptcha_valid  = $resp->success;
					$recaptcha_errors = $resp->errorCodes;
				}

				if ( ! $recaptcha_valid ) {
					$pmpro_msg  = sprintf( __( "reCAPTCHA failed. (%s) Please try again.", "pmpro" ), $recaptcha_errors );
					$pmpro_msgt = "pmpro_error";
				} else {
					// Your code here to handle a successful verification
					if ( $pmpro_msgt != "pmpro_error" ) {
						$pmpro_msg = "All good!";
					}
				}
			} else {
				if ( $pmpro_msgt != "pmpro_error" ) {
					$pmpro_msg = "All good!";
				}
			}

			//no errors yet
			if ( $pmpro_msgt != "pmpro_error" ) {
				do_action( 'pmpro_checkout_before_processing' );

				//process checkout if required
				if ( $pmpro_requirebilling ) {
					$morder                   = new MemberOrder();
					$morder->membership_id    = $pmpro_level->id;
					$morder->membership_name  = $pmpro_level->name;
					$morder->discount_code    = $discount_code;
					$morder->InitialPayment   = $pmpro_level->initial_payment;
					$morder->PaymentAmount    = $pmpro_level->billing_amount;
					$morder->ProfileStartDate = date_i18n( "Y-m-d", current_time( "timestamp" ) ) . "T0:0:0";
					$morder->BillingPeriod    = $pmpro_level->cycle_period;
					$morder->BillingFrequency = $pmpro_level->cycle_number;

					if ( $pmpro_level->billing_limit ) {
						$morder->TotalBillingCycles = $pmpro_level->billing_limit;
					}

					if ( pmpro_isLevelTrial( $pmpro_level ) ) {
						$morder->TrialBillingPeriod    = $pmpro_level->cycle_period;
						$morder->TrialBillingFrequency = $pmpro_level->cycle_number;
						$morder->TrialBillingCycles    = $pmpro_level->trial_limit;
						$morder->TrialAmount           = $pmpro_level->trial_amount;
					}

					//credit card values
					$morder->cardtype              = $CardType;
					$morder->accountnumber         = $AccountNumber;
					$morder->expirationmonth       = $ExpirationMonth;
					$morder->expirationyear        = $ExpirationYear;
					$morder->ExpirationDate        = $ExpirationMonth . $ExpirationYear;
					$morder->ExpirationDate_YdashM = $ExpirationYear . "-" . $ExpirationMonth;
					$morder->CVV2                  = $CVV;

					//not saving email in order table, but the sites need it
					$morder->Email = $bemail;

					//sometimes we need these split up
					$morder->FirstName = $bfirstname;
					$morder->LastName  = $blastname;
					$morder->Address1  = $baddress1;
					$morder->Address2  = $baddress2;

					//other values
					$morder->billing          = new stdClass();
					$morder->billing->name    = $bfirstname . " " . $blastname;
					$morder->billing->street  = trim( $baddress1 . " " . $baddress2 );
					$morder->billing->city    = $bcity;
					$morder->billing->state   = $bstate;
					$morder->billing->country = $bcountry;
					$morder->billing->zip     = $bzipcode;
					$morder->billing->phone   = $bphone;

					//$gateway = pmpro_getOption("gateway");
					$morder->gateway = $gateway;
					$morder->setGateway();

					//setup level var
					$morder->getMembershipLevel();
					$morder->membership_level = apply_filters( "pmpro_checkout_level", $morder->membership_level );

					//tax
					$morder->subtotal = $morder->InitialPayment;
					$morder->getTax();

					//filter for order, since v1.8
					$morder = apply_filters( "pmpro_checkout_order", $morder );

					$pmpro_processed = $morder->process();

					if ( ! empty( $pmpro_processed ) ) {
						$pmpro_msg       = __( "Payment accepted.", "pmpro" );
						$pmpro_msgt      = "pmpro_success";
						$pmpro_confirmed = true;
					} else {
						$pmpro_msg = $morder->error;
						if ( empty( $pmpro_msg ) ) {
							$pmpro_msg = __( "Unknown error generating account. Please contact us to set up your membership.", "pmpro" );
						}
						$pmpro_msgt = "pmpro_error";
					}

				} else // !$pmpro_requirebilling
				{
					//must have been a free membership, continue
					$pmpro_confirmed = true;
				}
			}
		}
	}    //endif ($pmpro_continue_registration)
}

//make sure we have at least an empty morder here to avoid a warning
if ( empty( $morder ) ) {
	$morder = false;
}

//Hook to check payment confirmation or replace it. If we get an array back, pull the values (morder) out
$pmpro_confirmed = apply_filters( 'pmpro_checkout_confirmed', $pmpro_confirmed, $morder );
if ( is_array( $pmpro_confirmed ) ) {
	extract( $pmpro_confirmed );
}

//if payment was confirmed create/update the user.
if ( ! empty( $pmpro_confirmed ) ) {
	//just in case this hasn't been set yet
	$submit = true;

	//do we need to create a user account?
	if ( ! $current_user->ID ) {
		/*
			create user
		*/
		if ( version_compare( $wp_version, "3.1" ) < 0 ) {
			require_once( ABSPATH . WPINC . '/registration.php' );
		}    //need this for WP versions before 3.1

		//first name
		if ( ! empty( $_REQUEST['first_name'] ) ) {
			$first_name = $_REQUEST['first_name'];
		} else {
			$first_name = $bfirstname;
		}
		//last name
		if ( ! empty( $_REQUEST['last_name'] ) ) {
			$last_name = $_REQUEST['last_name'];
		} else {
			$last_name = $blastname;
		}

		//insert user
		$new_user_array = apply_filters( 'pmpro_checkout_new_user_array', array(
				"user_login" => $username,
				"user_pass"  => $password,
				"user_email" => $bemail,
				"first_name" => $first_name,
				"last_name"  => $last_name
			)
		);

		$user_id = apply_filters( 'pmpro_new_user', '', $new_user_array );
		if ( empty( $user_id ) ) {
			$user_id = wp_insert_user( $new_user_array );
		}

		if ( empty( $user_id ) || is_wp_error( $user_id ) ) {
			$e_msg = '';

			if ( is_wp_error( $user_id ) ) {
				$e_msg = $user_id->get_error_message();
			}

			$pmpro_msg  = __( "Your payment was accepted, but there was an error setting up your account. Please contact us.", "pmpro" ) . sprintf( " %s", $e_msg ); // Dirty 'don't break translation hack.
			$pmpro_msgt = "pmpro_error";
		} elseif ( apply_filters( 'pmpro_setup_new_user', true, $user_id, $new_user_array, $pmpro_level ) ) {

			//check pmpro_wp_new_user_notification filter before sending the default WP email
			if ( apply_filters( "pmpro_wp_new_user_notification", true, $user_id, $pmpro_level->id ) ) {
				if ( version_compare( $wp_version, "4.3.0" ) >= 0 ) {
					wp_new_user_notification( $user_id, null, 'both' );
				} else {
					wp_new_user_notification( $user_id, $new_user_array['user_pass'] );
				}
			}

			$wpuser = get_userdata( $user_id );

			//make the user a subscriber
			$wpuser->set_role( get_option( 'default_role', 'subscriber' ) );

			//okay, log them in to WP
			$creds                  = array();
			$creds['user_login']    = $new_user_array['user_login'];
			$creds['user_password'] = $new_user_array['user_pass'];
			$creds['remember']      = true;
			$user                   = wp_signon( $creds, false );

			//setting some cookies
			wp_set_current_user( $user_id, $username );
			wp_set_auth_cookie( $user_id, true, apply_filters( 'pmpro_checkout_signon_secure', force_ssl_admin() ) );
		}
	} else {
		$user_id = $current_user->ID;
	}

	if ( ! empty( $user_id ) && ! is_wp_error( $user_id ) ) {
		do_action( 'pmpro_checkout_before_change_membership_level', $user_id, $morder );

		//start date is NOW() but filterable below
		$startdate = current_time( "mysql" );

		/**
		 * Filter the start date for the membership/subscription.
		 *
		 * @since 1.8.9
		 *
		 * @param string $startdate , datetime formatsted for MySQL (NOW() or YYYY-MM-DD)
		 * @param int $user_id , ID of the user checking out
		 * @param object $pmpro_level , object of level being checked out for
		 */
		$startdate = apply_filters( "pmpro_checkout_start_date", $startdate, $user_id, $pmpro_level );

		//calculate the end date
		if ( ! empty( $pmpro_level->expiration_number ) ) {
			$enddate =  date_i18n( "Y-m-d", strtotime( "+ " . $pmpro_level->expiration_number . " " . $pmpro_level->expiration_period, current_time( "timestamp" ) ) );
		} else {
			$enddate = "NULL";
		}

		/**
		 * Filter the end date for the membership/subscription.
		 *
		 * @since 1.8.9
		 *
		 * @param string $enddate , datetime formatsted for MySQL (YYYY-MM-DD)
		 * @param int $user_id , ID of the user checking out
		 * @param object $pmpro_level , object of level being checked out for
		 * @param string $startdate , startdate calculated above
		 */
		$enddate = apply_filters( "pmpro_checkout_end_date", $enddate, $user_id, $pmpro_level, $startdate );

		//check code before adding it to the order
		$code_check = pmpro_checkDiscountCode( $discount_code, $pmpro_level->id, true );
		if ( $code_check[0] == false ) {
			//error
			$pmpro_msg  = $code_check[1];
			$pmpro_msgt = "pmpro_error";

			//don't use this code
			$use_discount_code = false;
		} else {
			//all okay
			$use_discount_code = true;
		}
		
		//update membership_user table.		
		if ( ! empty( $discount_code ) && ! empty( $use_discount_code ) ) {
			$discount_code_id = $wpdb->get_var( "SELECT id FROM $wpdb->pmpro_discount_codes WHERE code = '" . esc_sql( $discount_code ) . "' LIMIT 1" );
		} else {
			$discount_code_id = "";
		}

		$custom_level = array(
			'user_id'         => $user_id,
			'membership_id'   => $pmpro_level->id,
			'code_id'         => $discount_code_id,
			'initial_payment' => $pmpro_level->initial_payment,
			'billing_amount'  => $pmpro_level->billing_amount,
			'cycle_number'    => $pmpro_level->cycle_number,
			'cycle_period'    => $pmpro_level->cycle_period,
			'billing_limit'   => $pmpro_level->billing_limit,
			'trial_amount'    => $pmpro_level->trial_amount,
			'trial_limit'     => $pmpro_level->trial_limit,
			'startdate'       => $startdate,
			'enddate'         => $enddate
		);

		if ( pmpro_changeMembershipLevel( $custom_level, $user_id, 'changed' ) ) {
			//we're good
			//blank order for free levels
			if ( empty( $morder ) ) {
				$morder                 = new MemberOrder();
				$morder->InitialPayment = 0;
				$morder->Email          = $bemail;
				$morder->gateway        = "free";

				$morder = apply_filters( "pmpro_checkout_order_free", $morder );
			}

			//add an item to the history table, cancel old subscriptions
			if ( ! empty( $morder ) ) {
				$morder->user_id       = $user_id;
				$morder->membership_id = $pmpro_level->id;
				$morder->saveOrder();
			}

			//update the current user
			global $current_user;
			if ( ! $current_user->ID && $user->ID ) {
				$current_user = $user;
			} //in case the user just signed up
			pmpro_set_current_user();

			//add discount code use
			if ( $discount_code && $use_discount_code ) {
				if ( ! empty( $morder->id ) ) {
					$code_order_id = $morder->id;
				} else {
					$code_order_id = "";
				}

				$wpdb->query( "INSERT INTO $wpdb->pmpro_discount_codes_uses (code_id, user_id, order_id, timestamp) VALUES('" . $discount_code_id . "', '" . $user_id . "', '" . intval( $code_order_id ) . "', '" . current_time( "mysql" ) . "')" );
			}

			//save billing info ect, as user meta
			$meta_keys   = array(
				"pmpro_bfirstname",
				"pmpro_blastname",
				"pmpro_baddress1",
				"pmpro_baddress2",
				"pmpro_bcity",
				"pmpro_bstate",
				"pmpro_bzipcode",
				"pmpro_bcountry",
				"pmpro_bphone",
				"pmpro_bemail",
				"pmpro_CardType",
				"pmpro_AccountNumber",
				"pmpro_ExpirationMonth",
				"pmpro_ExpirationYear"
			);
			$meta_values = array(
				$bfirstname,
				$blastname,
				$baddress1,
				$baddress2,
				$bcity,
				$bstate,
				$bzipcode,
				$bcountry,
				$bphone,
				$bemail,
				$CardType,
				hideCardNumber( $AccountNumber ),
				$ExpirationMonth,
				$ExpirationYear
			);
			pmpro_replaceUserMeta( $user_id, $meta_keys, $meta_values );

			//save first and last name fields
			if ( ! empty( $bfirstname ) ) {
				$old_firstname = get_user_meta( $user_id, "first_name", true );
				if ( empty( $old_firstname ) ) {
					update_user_meta( $user_id, "first_name", $bfirstname );
				}
			}
			if ( ! empty( $blastname ) ) {
				$old_lastname = get_user_meta( $user_id, "last_name", true );
				if ( empty( $old_lastname ) ) {
					update_user_meta( $user_id, "last_name", $blastname );
				}
			}

			//show the confirmation
			$ordersaved = true;

			//hook
			do_action( "pmpro_after_checkout", $user_id, $morder );    //added $morder param in v2.0

			$sendemails = apply_filters( "pmpro_send_checkout_emails", true);
	
			if($sendemails) { // Send the e-mails only if the flag is set to true

				//setup some values for the emails
				if ( ! empty( $morder ) ) {
					$invoice = new MemberOrder( $morder->id );
				} else {
					$invoice = null;
				}
				$current_user->membership_level = $pmpro_level; //make sure they have the right level info

				//send email to member
				$pmproemail = new PMProEmail();
				$pmproemail->sendCheckoutEmail( $current_user, $invoice );

				//send email to admin
				$pmproemail = new PMProEmail();
				$pmproemail->sendCheckoutAdminEmail( $current_user, $invoice );
			}

			//redirect to confirmation
			$rurl = pmpro_url( "confirmation", "?level=" . $pmpro_level->id );
			$rurl = apply_filters( "pmpro_confirmation_url", $rurl, $user_id, $pmpro_level );
			wp_redirect( $rurl );
			exit;
		} else {

			//uh oh. we charged them then the membership creation failed

			// test that the order object contains data
			$test = (array) $morder;
			if ( ! empty( $test ) && $morder->cancel() ) {
				$pmpro_msg = __( "IMPORTANT: Something went wrong during membership creation. Your credit card authorized, but we cancelled the order immediately. You should not try to submit this form again. Please contact the site owner to fix this issue.", "pmpro" );
				$morder    = null;
			} else {
				$pmpro_msg = __( "IMPORTANT: Something went wrong during membership creation. Your credit card was charged, but we couldn't assign your membership. You should not submit this form again. Please contact the site owner to fix this issue.", "pmpro" );
			}
		}
	}
}

//default values
if ( empty( $submit ) ) {
	//show message if the payment gateway is not setup yet
	if ( $pmpro_requirebilling && ! pmpro_getOption( "gateway", true ) ) {
		if ( pmpro_isAdmin() ) {
			$pmpro_msg = sprintf( __( 'You must <a href="%s">set up a Payment Gateway</a> before any payments will be processed.', 'pmpro' ), get_admin_url( null, '/admin.php?page=pmpro-paymentsettings' ) );
		} else {
			$pmpro_msg = __( "A Payment Gateway must be set up before any payments will be processed.", "pmpro" );
		}
		$pmpro_msgt = "";
	}

	//default values from DB
	if ( ! empty( $current_user->ID ) ) {
		$bfirstname    = get_user_meta( $current_user->ID, "pmpro_bfirstname", true );
		$blastname     = get_user_meta( $current_user->ID, "pmpro_blastname", true );
		$baddress1     = get_user_meta( $current_user->ID, "pmpro_baddress1", true );
		$baddress2     = get_user_meta( $current_user->ID, "pmpro_baddress2", true );
		$bcity         = get_user_meta( $current_user->ID, "pmpro_bcity", true );
		$bstate        = get_user_meta( $current_user->ID, "pmpro_bstate", true );
		$bzipcode      = get_user_meta( $current_user->ID, "pmpro_bzipcode", true );
		$bcountry      = get_user_meta( $current_user->ID, "pmpro_bcountry", true );
		$bphone        = get_user_meta( $current_user->ID, "pmpro_bphone", true );
		$bemail        = get_user_meta( $current_user->ID, "pmpro_bemail", true );
		$bconfirmemail = $bemail;    //as of 1.7.5, just setting to bemail
		$CardType      = get_user_meta( $current_user->ID, "pmpro_CardType", true );
		//$AccountNumber = hideCardNumber(get_user_meta($current_user->ID, "pmpro_AccountNumber", true), false);
		$ExpirationMonth = get_user_meta( $current_user->ID, "pmpro_ExpirationMonth", true );
		$ExpirationYear  = get_user_meta( $current_user->ID, "pmpro_ExpirationYear", true );
	}
}

//clear out XXXX numbers (e.g. with Stripe)
if ( ! empty( $AccountNumber ) && strpos( $AccountNumber, "XXXX" ) === 0 ) {
	$AccountNumber = "";
}
