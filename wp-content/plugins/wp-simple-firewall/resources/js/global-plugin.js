var iCWP_WPSF_Autoupdates = new function () {

	var bRequestCurrentlyRunning = false;

	var togglePluginUpdate = function ( event ) {
		if ( bRequestCurrentlyRunning ) {
			return false;
		}

		$oInput = jQuery( this );

		if ( $oInput.data( 'disabled' ) !== 'no' ) {
			iCWP_WPSF_Growl.showMessage( $oInput.data( 'disabled' ), false );
			return false;
		}

		return sendTogglePluginAutoupdate( $oInput.data( 'pluginfile' ), $oInput.data( 'nonce' ) );
	};

	var sendTogglePluginAutoupdate = function ( sPluginFile, sAjaxNonce ) {
		bRequestCurrentlyRunning = true;

		var requestData = {
			'action': 'icwp_wpsf_TogglePluginAutoupdate',
			'pluginfile': sPluginFile,
			'_ajax_nonce': sAjaxNonce
		};

		jQuery.post( ajaxurl, requestData,
			function ( oResponse ) {
				iCWP_WPSF_Growl.showMessage( oResponse.data.message, oResponse.success );
			}
		).always( function () {
				bRequestCurrentlyRunning = false;
			}
		);

		return true;
	};

	this.initialise = function () {
		jQuery( document ).ready( function () {
			jQuery( document ).on( "click", "input.icwp-autoupdate-plugin", togglePluginUpdate );
		} );
	};

}();

var iCWP_WPSF_OptionsFormSubmit = new function () {

	var bRequestCurrentlyRunning = false;

	this.submit = function ( sMessage, bSuccess ) {
		var $oDiv = createDynDiv( bSuccess ? 'success' : 'failed' );
		$oDiv.fadeIn().html( sMessage );
		setTimeout( function () {
			$oDiv.fadeOut( 5000 );
			$oDiv.remove();
		}, 4000 );
	};

	/**
	 */
	var submitOptionsForm = function ( event ) {
		iCWP_WPSF_BodyOverlay.show();

		if ( bRequestCurrentlyRunning ) {
			return false;
		}

		bRequestCurrentlyRunning = true;

		event.preventDefault();

		var $oForm = jQuery( this );
		jQuery( '<input />' ).attr( 'type', 'hidden' )
							 .attr( 'name', 'action' )
							 .attr( 'value', 'icwp_OptionsFormSave' )
							 .appendTo( $oForm );

		jQuery.post( ajaxurl, $oForm.serialize(),
			function ( oResponse ) {
				var sMessage;
				if ( oResponse.data.message === undefined ) {
					sMessage = oResponse.success ? 'Success' : 'Failure';
				}
				else {
					sMessage = oResponse.data.message;
				}
				jQuery('div#icwpOptionsFormContainer').html( oResponse.data.options_form )
				iCWP_WPSF_Growl.showMessage( sMessage, oResponse.success );
			}
		).always( function () {
				bRequestCurrentlyRunning = false;
				iCWP_WPSF_BodyOverlay.hide();
			}
		);
	};

	this.initialise = function () {
		jQuery( document ).ready( function () {
			jQuery( document ).on( "submit", "form.icwpOptionsForm", submitOptionsForm );
		} );
	};
}();

var iCWP_WPSF_Growl = new function () {

	this.showMessage = function ( sMessage, bSuccess ) {
		var $oDiv = createDynDiv( bSuccess ? 'success' : 'failed' );
		$oDiv.show().addClass( 'shown' );
		setTimeout( function () {
			$oDiv.html( sMessage );
		}, 300 );
		setTimeout( function () {
			$oDiv.css( 'width', 0 );
		}, 4000 );
		setTimeout( function () {
			$oDiv.html( '' )
				 .fadeOut();
		}, 4500 );
	};

	/**
	 */
	var createDynDiv = function ( sClass ) {
		var $oDiv = jQuery( '<div />' ).appendTo( 'body' );
		$oDiv.attr( 'id', 'icwp-growl-notice' + Math.floor( (Math.random() * 100) + 1 ) );
		$oDiv.addClass( sClass ).addClass( 'icwp-growl-notice' );
		return $oDiv;
	};

}();

var iCWP_WPSF_BodyOverlay = new function () {

	this.show = function () {
		jQuery( 'div#icwp-fade-wrapper' ).fadeIn( 1500 );
	};

	this.hide = function () {
		jQuery( 'div#icwp-fade-wrapper' ).stop().fadeOut();
	};

	this.initialise = function () {
		jQuery( document ).ready( function () {
			var $oDiv = jQuery( '<div />' )
			.attr( 'id', 'icwp-fade-wrapper' )
			.html( '<div class="icwp-waiting"></div>' )
			.appendTo( 'body' );
		} );
	};

}();

iCWP_WPSF_Autoupdates.initialise();
iCWP_WPSF_OptionsFormSubmit.initialise();
iCWP_WPSF_BodyOverlay.initialise();