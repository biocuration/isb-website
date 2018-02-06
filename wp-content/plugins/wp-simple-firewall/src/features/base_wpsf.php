<?php

if ( class_exists( 'ICWP_WPSF_FeatureHandler_BaseWpsf', false ) ) {
	return;
}

require_once( dirname( __FILE__ ).DIRECTORY_SEPARATOR.'base.php' );

class ICWP_WPSF_FeatureHandler_BaseWpsf extends ICWP_WPSF_FeatureHandler_Base {

	/**
	 * @var ICWP_WPSF_Processor_Sessions
	 */
	static protected $oSessProcessor;

	/**
	 * @return ICWP_WPSF_Processor_Sessions
	 */
	public function getSessionsProcessor() {
		return self::$oSessProcessor;
	}

	/**
	 * @return ICWP_WPSF_SessionVO|null
	 */
	public function getSession() {
		$oP = $this->getSessionsProcessor();
		return is_null( $oP ) ? null : $oP->getCurrentSession();
	}

	/**
	 * @return bool
	 */
	public function hasSession() {
		return !is_null( $this->getSession() );
	}

	/**
	 * @return array
	 */
	protected function getGoogleRecaptchaConfig() {
		$aConfig = apply_filters( $this->prefix( 'google_recaptcha_config' ), array() );
		if ( !is_array( $aConfig ) ) {
			$aConfig = array();
		}
		return array_merge(
			array(
				'key'    => '',
				'secret' => '',
				'style'  => 'light',
			),
			$aConfig
		);
	}

	/**
	 * Overridden in the plugin handler getting the option value
	 * @return string
	 */
	public function getGoogleRecaptchaSecretKey() {
		$aConfig = $this->getGoogleRecaptchaConfig();
		return $aConfig[ 'secret' ];
	}

	/**
	 * Overriden in the plugin handler getting the option value
	 * @return string
	 */
	public function getGoogleRecaptchaSiteKey() {
		$aConfig = $this->getGoogleRecaptchaConfig();
		return $aConfig[ 'key' ];
	}

	/**
	 * Overriden in the plugin handler getting the option value
	 * @return string
	 */
	public function getGoogleRecaptchaStyle() {
		$aConfig = $this->getGoogleRecaptchaConfig();
		return $aConfig[ 'style' ];
	}

	/**
	 * @return bool
	 */
	public function getIsGoogleRecaptchaReady() {
		$sKey = $this->getGoogleRecaptchaSiteKey();
		$sSecret = $this->getGoogleRecaptchaSecretKey();
		return ( !empty( $sSecret ) && !empty( $sKey ) && $this->loadDataProcessor()->getPhpSupportsNamespaces() );
	}

	/**
	 * @return array
	 */
	protected function getBaseDisplayData() {
		return $this->loadDP()->mergeArraysRecursive(
			parent::getBaseDisplayData(),
			array(
				'strings' => array(
					'go_to_settings'                    => _wpsf__( 'Settings' ),
					'on'                                => _wpsf__( 'On' ),
					'off'                               => _wpsf__( 'Off' ),
					'more_info'                         => _wpsf__( 'More Info' ),
					'blog'                              => _wpsf__( 'Blog' ),
					'plugin_activated_features_summary' => _wpsf__( 'Plugin Activated Features Summary:' ),
					'save_all_settings'                 => _wpsf__( 'Save All Settings' ),
					'options_title'                     => _wpsf__( 'Options' ),
					'options_summary'                   => _wpsf__( 'Configure Module' ),
					'actions_title'                     => _wpsf__( 'Actions and Info' ),
					'actions_summary'                   => _wpsf__( 'Perform actions for this module' ),
					'help_title'                        => _wpsf__( 'Help' ),
					'help_summary'                      => _wpsf__( 'Learn More' ),

					'aar_what_should_you_enter'    => _wpsf__( 'What should you enter here?' ),
					'aar_must_supply_key_first'    => _wpsf__( 'At some point you entered a Security Admin Access Key - to manage this plugin, you must supply it here first.' ),
					'aar_to_manage_must_enter_key' => _wpsf__( 'To manage this plugin you must enter the access key.' ),
					'aar_enter_access_key'         => _wpsf__( 'Enter Access Key' ),
					'aar_submit_access_key'        => _wpsf__( 'Submit Security Admin Key' )
				),
				'flags'   => array(
					'show_summary' => true,
					'has_session'  => $this->hasSession()
				)
			)
		);
	}

	protected function getTranslatedString( $sKey, $sDefault ) {
		$aStrings = array(
			'nonce_failed_empty'    => _wpsf__( 'Nonce security checking failed - the nonce value was empty.' ),
			'nonce_failed_supplied' => _wpsf__( 'Nonce security checking failed - the nonce supplied was "%s".' ),
		);
		return ( isset( $aStrings[ $sKey ] ) ? $aStrings[ $sKey ] : $sDefault );
	}

	/**
	 * @return bool
	 */
	protected function isVisitorWhitelisted() {
		return apply_filters( $this->prefix( 'visitor_is_whitelisted' ), false );
	}

	/**
	 * @param array $aOptionsParams
	 * @return array
	 * @throws Exception
	 */
	protected function loadStrings_SectionTitlesDefaults( $aOptionsParams ) {

		switch ( $aOptionsParams[ 'slug' ] ) {

			case 'section_user_messages' :
				$sTitle = _wpsf__( 'User Messages' );
				$sTitleShort = _wpsf__( 'User Messages' );
				$aSummary = array(
					sprintf( _wpsf__( 'Purpose - %s' ), _wpsf__( 'Customize the messages displayed to the user.' ) ),
					sprintf( _wpsf__( 'Recommendation - %s' ), _wpsf__( 'Use this section if you need to communicate to the user in a particular manner.' ) ),
					sprintf( _wpsf__( 'Hint - %s' ), sprintf( _wpsf__( 'To reset any message to its default, enter the text exactly: %s' ), 'default' ) )
				);
				break;

			default:
				throw new Exception( sprintf( 'A section slug was defined but with no associated strings. Slug: "%s".', $aOptionsParams[ 'slug' ] ) );
		}

		return array( $sTitle, $sTitleShort, $aSummary );
	}
}