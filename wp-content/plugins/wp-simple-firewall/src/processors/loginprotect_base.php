<?php

if ( !class_exists( 'ICWP_WPSF_Processor_LoginProtect_Base', false ) ):

require_once( dirname(__FILE__).DIRECTORY_SEPARATOR.'base_wpsf.php' );

abstract class ICWP_WPSF_Processor_LoginProtect_Base extends ICWP_WPSF_Processor_BaseWpsf {

	protected function setLoginAsFailed( $sStatToIncrement ) {
		
		remove_filter( 'authenticate', 'wp_authenticate_username_password', 20 );  // wp-includes/user.php
		remove_filter( 'authenticate', 'wp_authenticate_email_password', 20 );  // wp-includes/user.php

		$this->doStatIncrement( $sStatToIncrement );

		// We now black mark this IP
		add_filter( $this->getFeature()->prefix( 'ip_black_mark' ), '__return_true' );
	}
}
endif;