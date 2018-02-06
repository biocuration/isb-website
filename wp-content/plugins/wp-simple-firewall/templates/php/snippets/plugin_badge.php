<style type="text/css">
	#icwpWpsfSiteBadge {
        background-color: rgba(241, 241, 241, 0.99);
        border-radius: 5px;
        bottom: 9px;
        box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.47);
        box-sizing: content-box;
        color: #000000;
        height: 33px;
        left: 9px;
        opacity: 0.9;
        padding: 7px 4px 7px 10px;
        position: fixed;
        text-align: left;
        width: 180px;
        z-index: 10000;
        -webkit-transition: width 0.5s; /* Safari */
        transition: width 0.5s;
	}
    #icwpWpsfSiteBadge:hover {
        opacity: 1.0;
    }
	#icwpWpsfSiteBadge a {
		border: 0 none;
		box-sizing: inherit;
		color: inherit !important;
		display: block;
		line-height: 8px;
		padding: 0;
		text-decoration: none !important;
	}
	#icwpWpsfSiteBadge a:hover {
		text-decoration: none;
	}
	#icwpWpsfSiteBadge .badge-text {
        font-family: sans-serif;
		font-size: 12px;
		line-height: 17px;
		padding-left: 43px;
		letter-spacing: 0;
        -webkit-transition: opacity 1s; /* Safari */
        transition: opacity 1s;
	}
	#icwpWpsfSiteBadge img {
		float: left;
	}
	a#icwpWpsfCloseButton {
        background-color: #ffffff;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 10px;
        height: 10px;
        left: -2px;
        line-height: 6px;
        padding: 2px 1px 0 2px !important;
        position: absolute;
        text-align: center;
        top: -3px;
        width: 10px;
        z-index: 1001;
	}
	#icwpWpsfCloseButton:hover {
		cursor: pointer;
	}
    @media (max-width: 600px) {
        #icwpWpsfSiteBadge {
            height: 16px;
            width: 112px;
        }
        #icwpWpsfSiteBadge .badge-text {
            font-size: 8px;
            line-height: 10px;
            padding-left: 20px;
        }
        #icwpWpsfSiteBadge img {
            height: 16px;
            width: 16px;
        }
    }
</style>
<div id="icwpWpsfSiteBadge">
	<a id="icwpWpsfCloseButton">x</a>
	<a href="http://icwp.io/wpsecurityfirewall" target="_blank"
	   title="This site is protected by the Shield Security plugin."
	   %s>
		<img src="%s" alt="%s Logo" />
        <div class="badge-text">%s</div>
	</a>
</div>

<script type="text/javascript">
	var iCWP_WPSF_PluginBadgeClose = new function () {

		var closeBadge = function () {

			var requestData = {
				'action': '<?php echo $icwp_ajax_action; ?>',
				'icwp_ajax_action': '<?php echo $icwp_ajax_action; ?>',
				'icwp_nonce': '<?php echo $icwp_nonce; ?>',
				'icwp_nonce_action': '<?php echo $icwp_nonce_action; ?>',
				'icwp_action_module': '<?php echo $icwp_action_module; ?>'
			};

			jQuery.post( '<?php echo $ajaxurl; ?>', requestData,
				function ( oResponse ) {
				}
			).always( function () {
				jQuery( '#icwpWpsfSiteBadge' ).remove();
				}
			);

			return true;
		};

		this.initialise = function () {
			jQuery( document ).ready( function () {
				jQuery( document ).on( "click", "a#icwpWpsfCloseButton", closeBadge );
			} );
		};

	}();

	iCWP_WPSF_PluginBadgeClose.initialise();
</script>
