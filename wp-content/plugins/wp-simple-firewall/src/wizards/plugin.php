<?php

if ( class_exists( 'ICWP_WPSF_Wizard_Plugin', false ) ) {
	return;
}

require_once( dirname( __FILE__ ).'/base_wpsf.php' );

/**
 * Class ICWP_WPSF_Processor_LoginProtect_Wizard
 */
class ICWP_WPSF_Wizard_Plugin extends ICWP_WPSF_Wizard_BaseWpsf {

	/**
	 * @return string[]
	 */
	protected function getSupportedWizards() {
		return array( 'welcome', 'importexport' );
	}

	/**
	 * @return string
	 */
	protected function getPageTitle() {
		return sprintf( _wpsf__( '%s Welcome Wizard' ), $this->getPluginCon()->getHumanName() );
	}

	/**
	 * @param string $sStep
	 * @return \FernleafSystems\Utilities\Response|null
	 */
	protected function processWizardStep( $sStep ) {
		switch ( $sStep ) {

			case 'ip_detect':
				$oResponse = $this->wizardIpDetect();
				break;

			case 'license':
				$oResponse = $this->wizardLicense();
				break;

			case 'import':
				$oResponse = $this->wizardImportOptions();
				break;

			case 'admin_access_restriction':
				$oResponse = $this->wizardSecurityAdmin();
				break;

			case 'audit_trail':
				$oResponse = $this->wizardAuditTrail();
				break;

			case 'ips':
				$oResponse = $this->wizardIps();
				break;

			case 'comments_filter':
				$oResponse = $this->wizardCommentsFilter();
				break;

			case 'login_protect':
				$oResponse = $this->wizardLoginProtect();
				break;

			case 'optin_usage':
			case 'optin_badge':
				$oResponse = $this->wizardOptin();
				break;

			default:
				$oResponse = parent::processWizardStep( $sStep );
				break;
		}
		return $oResponse;
	}

	/**
	 * @return string[]
	 * @throws Exception
	 */
	protected function determineWizardSteps() {

		switch ( $this->getWizardSlug() ) {
			case 'welcome':
				$aSteps = $this->determineWizardSteps_Welcome();
				break;
			case 'importexport':
				$aSteps = $this->determineWizardSteps_Import();
				break;
			default:
				parent::determineWizardSteps();
				break;
		}
		return array_values( array_intersect( array_keys( $this->getAllDefinedSteps() ), $aSteps ) );
	}

	/**
	 * @return string[]
	 */
	private function determineWizardSteps_Import() {
		return array(
			'start',
			'import',
			'finished',
		);
	}

	/**
	 * @return string[]
	 */
	private function determineWizardSteps_Welcome() {
		/** @var ICWP_WPSF_FeatureHandler_Plugin $oFO */
		$oFO = $this->getModCon();
		$oConn = $this->getPluginCon();

		$aStepsSlugs = array(
			'welcome',
			'ip_detect'
		);
//		if ( !$oFO->isPremium() ) {
//			$aStepsSlugs[] = 'license'; not showing it for now
//		}

		if ( $oFO->isPremium() ) {
			$aStepsSlugs[] = 'import';
		}

		if ( !$oConn->getModule( 'admin_access_restriction' )->getIsMainFeatureEnabled() ) {
			$aStepsSlugs[] = 'admin_access_restriction';
		}

		/** @var ICWP_WPSF_FeatureHandler_AuditTrail $oModule */
		$oModule = $oConn->getModule( 'audit_trail' );
		if ( !$oModule->getIsMainFeatureEnabled() ) {
			$aStepsSlugs[] = 'audit_trail';
		}

		if ( !$oConn->getModule( 'ips' )->getIsMainFeatureEnabled() ) {
			$aStepsSlugs[] = 'ips';
		}

		/** @var ICWP_WPSF_FeatureHandler_LoginProtect $oModule */
		$oModule = $oConn->getModule( 'login_protect' );
		if ( !( $oModule->getIsMainFeatureEnabled() && $oModule->isEnabledGaspCheck() ) ) {
			$aStepsSlugs[] = 'login_protect';
		}

		/** @var ICWP_WPSF_FeatureHandler_CommentsFilter $oModule */
		$oModule = $oConn->getModule( 'comments_filter' );
		if ( !( $oModule->getIsMainFeatureEnabled() && $oModule->isEnabledGaspCheck() ) ) {
			$aStepsSlugs[] = 'comments_filter';
		}

		$aStepsSlugs[] = 'how_shield_works';
		$aStepsSlugs[] = 'optin';

		if ( !$oFO->isPremium() ) {
			$aStepsSlugs[] = 'import';
		}

		$aStepsSlugs[] = 'thankyou';
		return $aStepsSlugs;
	}

	/**
	 * @param string $sStep
	 * @return array
	 */
	protected function getRenderData_SlideExtra( $sStep ) {
		$oConn = $this->getPluginCon();

		$aAdditional = array();

		$sCurrentWiz = $this->getWizardSlug();

		if ( $sCurrentWiz == 'welcome' ) {

			switch ( $sStep ) {
				case 'ip_detect':
					$aAdditional = array(
						'hrefs' => array(
							'visitor_ip' => 'http://icwp.io/visitorip',
						)
					);
					break;
				case 'license':
					break;
				case 'import':
					$aAdditional = array(
						'hrefs' => array(
							'blog_importexport' => 'http://icwp.io/av'
						),
						'imgs'  => array(
							'shieldnetworkmini' => $oConn->getPluginUrl_Image( 'shield/shieldnetworkmini.png' ),
						)
					);
					break;

				case 'optin':
					$oUser = $this->loadWpUsers()->getCurrentWpUser();
					$aAdditional = array(
						'data' => array(
							'name'       => $oUser->first_name,
							'user_email' => $oUser->user_email
						)
					);
					break;

				case 'thankyou':
					break;

				case 'how_shield_works':
					$aAdditional = array(
						'imgs'     => array(
							'how_shield_works' => $oConn->getPluginUrl_Image( 'wizard/general-shield_where.png' ),
							'modules'          => $oConn->getPluginUrl_Image( 'wizard/general-shield_modules.png' ),
							'options'          => $oConn->getPluginUrl_Image( 'wizard/general-shield_options.png' ),
							'help'             => $oConn->getPluginUrl_Image( 'wizard/general-shield_help.png' ),
							'actions'          => $oConn->getPluginUrl_Image( 'wizard/general-shield_actions.png' ),
							'module_onoff'     => $oConn->getPluginUrl_Image( 'wizard/general-module_onoff.png' ),
							'option_help'      => $oConn->getPluginUrl_Image( 'wizard/general-option_help.png' ),
						),
						'headings' => array(
							'how_shield_works' => _wpsf__( 'Where to find Shield' ),
							'modules'          => _wpsf__( 'Accessing Each Module' ),
							'options'          => _wpsf__( 'Accessing Options' ),
							'help'             => _wpsf__( 'Finding Help' ),
							'actions'          => _wpsf__( 'Actions (not Options)' ),
							'module_onoff'     => _wpsf__( 'Module On/Off Switch' ),
							'option_help'      => _wpsf__( 'Help For Each Option' ),
						),
						'captions' => array(
							'how_shield_works' => _wpsf__( "You'll find the main Shield Security setting in the left-hand WordPress menu." ),
							'modules'          => _wpsf__( 'Shield is split up into independent modules for accessing the options of each feature.' ),
							'options'          => _wpsf__( 'When you load a module, you can access the options by clicking on the Options Panel link.' ),
							'help'             => _wpsf__( 'Each module also has a brief overview help section - there is more in-depth help available.' ),
							'actions'          => _wpsf__( 'Certain modules have extra actions and features, e.g. Audit Trail Viewer.' )
												  .' '._wpsf__( 'Note: Not all modules have the actions section' ),
							'module_onoff'     => _wpsf__( 'Each module has an Enable/Disable checkbox to turn on/off all processing for that module' ),
							'option_help'      => _wpsf__( 'To help you understand each option, most of them have a more info link, and/or a blog link, to read more' ),
						),
					);
					break;
				default:
					break;
			}
		}
		else if ( $sCurrentWiz == 'importexport' ) {
			switch ( $sStep ) {
				case 'import':
					$aAdditional = array(
						'hrefs' => array(
							'blog_importexport' => 'http://icwp.io/av'
						),
						'imgs'  => array(
							'shieldnetworkmini' => $oConn->getPluginUrl_Image( 'shield/shieldnetworkmini.png' ),
						)
					);
					break;

				default:
					break;
			}
		}

		if ( empty( $aAdditional ) ) {
			$aAdditional = parent::getRenderData_SlideExtra( $sStep );
		}
		return $aAdditional;
	}

	/**
	 * @return \FernleafSystems\Utilities\Response
	 */
	private function wizardIpDetect() {
		$oIps = $this->loadIpService();
		$sIp = $this->loadDP()->post( 'ip' );

		$oResponse = new \FernleafSystems\Utilities\Response();
		$oResponse->setSuccessful( false );
		if ( empty( $sIp ) ) {
			$sMessage = 'IP address was empty.';
		}
		else if ( !$oIps->isValidIp_PublicRemote( $sIp ) ) {
			$sMessage = 'The IP address supplied was not a valid public IP address.';
		}
//		else if ( $oIps->getIpVersion( $sIp ) != 4 ) {
//			$sMessage = 'The IP address supplied was not a valid IP address.';
//		}
		else {
			$sSource = $oIps->determineSourceFromIp( $sIp );
			if ( empty( $sSource ) ) {
				$sMessage = 'Strange, the address source could not be found from this IP.';
			}
			else {
				/** @var ICWP_WPSF_FeatureHandler_Plugin $oModule */
				$oModule = $this->getPluginCon()->getModule( 'plugin' );
				$oModule->setVisitorAddressSource( $sSource )
						->savePluginOptions();
				$oResponse->setSuccessful( true );
				$sMessage = _wpsf__( 'Success!' ).' '
							.sprintf( '"%s" was found to be the best source of visitor IP addresses for your site.', $sSource );
			}
		}

		return $oResponse->setMessageText( $sMessage );
	}

	/**
	 * @return \FernleafSystems\Utilities\Response
	 */
	private function wizardLicense() {
		$sKey = $this->loadDP()->post( 'LicenseKey' );

		$bSuccess = false;
		if ( empty( $sKey ) ) {
			$sMessage = 'License key was empty.';
		}
		else {
			/** @var ICWP_WPSF_FeatureHandler_License $oModule */
			$oModule = $this->getPluginCon()->getModule( 'license' );
			try {
				$oModule->activateOfficialLicense( $sKey, true );
				if ( $oModule->hasValidWorkingLicense() ) {
					$bSuccess = true;
					$sMessage = _wpsf__( 'License key was accepted and installed successfully.' );
				}
				else {
					$sMessage = _wpsf__( 'License key was not accepted.' );
				}
			}
			catch ( Exception $oE ) {
				$sMessage = _wpsf__( $oE->getMessage() );
			}
		}

		$oResponse = new \FernleafSystems\Utilities\Response();
		return $oResponse->setSuccessful( $bSuccess )
						 ->setMessageText( $sMessage );
	}

	/**
	 * @return \FernleafSystems\Utilities\Response
	 */
	private function wizardImportOptions() {
		/** @var ICWP_WPSF_FeatureHandler_Plugin $oFO */
		$oFO = $this->getModCon();
		$oDP = $this->loadDP();

		$sMasterSiteUrl = $oDP->post( 'MasterSiteUrl' );
		$sSecretKey = $oDP->post( 'MasterSiteSecretKey' );
		$bEnabledNetwork = $oDP->post( 'ShieldNetworkCheck' ) === 'Y';

		/** @var ICWP_WPSF_Processor_Plugin $oProc */
		$oProc = $oFO->getProcessor();
		$nCode = $oProc->getSubProcessorImportExport()
					   ->runImport( $sMasterSiteUrl, $sSecretKey, $bEnabledNetwork, $sSiteResponse );

		$aErrors = array(
			_wpsf__( 'Options imported successfully to your site.' ), // success
			_wpsf__( 'Secret key was empty.' ),
			_wpsf__( 'Secret key was not 40 characters long.' ),
			_wpsf__( 'Secret key contains invalid characters - it should be letters and numbers only.' ),
			_wpsf__( 'Source site URL could not be parsed correctly.' ),
			_wpsf__( 'Could not parse the response from the site.' )
			.' '._wpsf__( 'Check the secret key is correct for the remote site.' ),
			_wpsf__( 'Failure response returned from the site.' ),
			sprintf( _wpsf__( 'Remote site responded with - %s' ), $sSiteResponse ),
			_wpsf__( 'Data returned from the site was empty.' )
		);

		$sMessage = isset( $aErrors[ $nCode ] ) ? $aErrors[ $nCode ] : 'Unknown Error';

		$oResponse = new \FernleafSystems\Utilities\Response();
		return $oResponse->setSuccessful( $nCode === 0 )
						 ->setMessageText( $sMessage );
	}

	/**
	 * @return \FernleafSystems\Utilities\Response
	 */
	private function wizardSecurityAdmin() {
		$oDP = $this->loadDP();
		$sKey = $oDP->post( 'AccessKey' );
		$sConfirm = $oDP->post( 'AccessKeyConfirm' );

		$oResponse = new \FernleafSystems\Utilities\Response();

		$bSuccess = false;
		if ( empty( $sKey ) ) {
			$sMessage = 'Security access key was empty.';
		}
		else if ( $sKey != $sConfirm ) {
			$sMessage = 'Keys do not match.';
		}
		else {
			/** @var ICWP_WPSF_FeatureHandler_AdminAccessRestriction $oModule */
			$oModule = $this->getPluginCon()->getModule( 'admin_access_restriction' );
			try {
				$oModule->setNewAccessKeyManually( $sKey )
						->setPermissionToSubmit( true );
				$bSuccess = true;
				$sMessage = _wpsf__( 'Security Admin setup was successful.' );
			}
			catch ( Exception $oE ) {
				$sMessage = _wpsf__( $oE->getMessage() );
			}
		}

		return $oResponse->setSuccessful( $bSuccess )
						 ->setMessageText( $sMessage );
	}

	/**
	 * @return \FernleafSystems\Utilities\Response
	 */
	private function wizardAuditTrail() {

		$sInput = $this->loadDP()->post( 'AuditTrailOption' );
		$bSuccess = false;
		$sMessage = _wpsf__( 'No changes were made as no option was selected' );

		if ( !empty( $sInput ) ) {
			$bEnabled = $sInput === 'Y';

			/** @var ICWP_WPSF_FeatureHandler_AdminAccessRestriction $oModule */
			$oModule = $this->getPluginCon()->getModule( 'audit_trail' );
			$oModule->setIsMainFeatureEnabled( $bEnabled )
					->savePluginOptions();

			$bSuccess = $oModule->getIsMainFeatureEnabled() === $bEnabled;
			if ( $bSuccess ) {
				$sMessage = sprintf( '%s has been %s.', _wpsf__( 'Audit Trail' ),
					$oModule->getIsMainFeatureEnabled() ? _wpsf__( 'Enabled' ) : _wpsf__( 'Disabled' )
				);
			}
			else {
				$sMessage = sprintf( _wpsf__( '%s setting could not be changed at this time.' ), _wpsf__( 'Audit Trail' ) );
			}
		}

		$oResponse = new \FernleafSystems\Utilities\Response();
		return $oResponse->setSuccessful( $bSuccess )
						 ->setMessageText( $sMessage );
	}

	/**
	 * @return \FernleafSystems\Utilities\Response
	 */
	private function wizardIps() {

		$sInput = $this->loadDP()->post( 'IpManagerOption' );
		$bSuccess = false;
		$sMessage = _wpsf__( 'No changes were made as no option was selected' );

		if ( !empty( $sInput ) ) {
			$bEnabled = $sInput === 'Y';

			/** @var ICWP_WPSF_FeatureHandler_Ips $oModule */
			$oModule = $this->getPluginCon()->getModule( 'ips' );
			$oModule->setIsMainFeatureEnabled( $bEnabled )
					->savePluginOptions();

			$bSuccess = $oModule->getIsMainFeatureEnabled() === $bEnabled;
			if ( $bSuccess ) {
				$sMessage = sprintf( '%s has been %s.', _wpsf__( 'IP Manager' ),
					$oModule->getIsMainFeatureEnabled() ? _wpsf__( 'Enabled' ) : _wpsf__( 'Disabled' )
				);
			}
			else {
				$sMessage = sprintf( _wpsf__( '%s setting could not be changed at this time.' ), _wpsf__( 'IP Manager' ) );
			}
		}

		$oResponse = new \FernleafSystems\Utilities\Response();
		return $oResponse->setSuccessful( $bSuccess )
						 ->setMessageText( $sMessage );
	}

	/**
	 * @return \FernleafSystems\Utilities\Response
	 */
	private function wizardLoginProtect() {

		$sInput = $this->loadDP()->post( 'LoginProtectOption' );
		$bSuccess = false;
		$sMessage = _wpsf__( 'No changes were made as no option was selected' );

		if ( !empty( $sInput ) ) {
			$bEnabled = $sInput === 'Y';

			/** @var ICWP_WPSF_FeatureHandler_LoginProtect $oModule */
			$oModule = $this->getPluginCon()->getModule( 'login_protect' );
			if ( $bEnabled ) { // we don't disable the whole module
				$oModule->setIsMainFeatureEnabled( true );
			}
			$oModule->setEnabledGaspCheck( $bEnabled )
					->savePluginOptions();

			$bSuccess = $oModule->getIsMainFeatureEnabled() === $bEnabled;
			if ( $bSuccess ) {
				$sMessage = sprintf( '%s has been %s.', _wpsf__( 'Login Protection' ),
					$oModule->getIsMainFeatureEnabled() ? _wpsf__( 'Enabled' ) : _wpsf__( 'Disabled' )
				);
			}
			else {
				$sMessage = sprintf( _wpsf__( '%s setting could not be changed at this time.' ), _wpsf__( 'Login Protection' ) );
			}
		}

		$oResponse = new \FernleafSystems\Utilities\Response();
		return $oResponse->setSuccessful( $bSuccess )
						 ->setMessageText( $sMessage );
	}

	/**
	 * @return \FernleafSystems\Utilities\Response
	 */
	private function wizardOptin() {
		$oDP = $this->loadDP();
		/** @var ICWP_WPSF_FeatureHandler_Plugin $oModule */
		$oModule = $this->getPluginCon()->getModule( 'plugin' );

		$bSuccess = false;
		$sMessage = _wpsf__( 'No changes were made as no option was selected' );

		$sForm = $oDP->post( 'wizard-step' );
		if ( $sForm == 'optin_badge' ) {
			$sInput = $oDP->post( 'BadgeOption' );

			if ( !empty( $sInput ) ) {
				$bEnabled = $sInput === 'Y';
				$oModule->setIsDisplayPluginBadge( $bEnabled );
				$bSuccess = true;
				$sMessage = _wpsf__( 'Preferences have been saved.' );
			}
		}
		else if ( $sForm == 'optin_badge' ) {
			$sInput = $oDP->post( 'AnonymousOption' );

			if ( !empty( $sInput ) ) {
				$bEnabled = $sInput === 'Y';
				$oModule->setPluginTrackingPermission( $bEnabled );
				$bSuccess = true;
				$sMessage = _wpsf__( 'Preferences have been saved.' );
			}
		}

		$oResponse = new \FernleafSystems\Utilities\Response();
		return $oResponse->setSuccessful( $bSuccess )
						 ->setMessageText( $sMessage );
	}

	/**
	 * @return \FernleafSystems\Utilities\Response
	 */
	private function wizardCommentsFilter() {

		$sInput = $this->loadDP()->post( 'CommentsFilterOption' );
		$bSuccess = false;
		$sMessage = _wpsf__( 'No changes were made as no option was selected' );

		if ( !empty( $sInput ) ) {
			$bEnabled = $sInput === 'Y';

			/** @var ICWP_WPSF_FeatureHandler_CommentsFilter $oModule */
			$oModule = $this->getPluginCon()->getModule( 'comments_filter' );
			if ( $bEnabled ) { // we don't disable the whole module
				$oModule->setIsMainFeatureEnabled( true );
			}
			$oModule->setEnabledGasp( $bEnabled )
					->savePluginOptions();

			$bSuccess = $oModule->getIsMainFeatureEnabled() === $bEnabled;
			if ( $bSuccess ) {
				$sMessage = sprintf( '%s has been %s.', _wpsf__( 'Comment SPAM Protection' ),
					$oModule->getIsMainFeatureEnabled() ? _wpsf__( 'Enabled' ) : _wpsf__( 'Disabled' )
				);
			}
			else {
				$sMessage = sprintf( _wpsf__( '%s setting could not be changed at this time.' ), _wpsf__( 'Comment SPAM Protection' ) );
			}
		}

		$oResponse = new \FernleafSystems\Utilities\Response();
		return $oResponse->setSuccessful( $bSuccess )
						 ->setMessageText( $sMessage );
	}
}