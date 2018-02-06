<?php
/**
 * Admin header template.
 *
 * The template wrapper for the admin header.
 *
 * @package SimpleShareButtonsAdder
 */

?>
<div class="ssba-admin-wrap">
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only"><?php echo esc_html__( 'Toggle navigation', 'simple-share-buttons-adder' ); ?></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="https://simplesharebuttons.com">
					<img src="<?php echo esc_url( plugins_url() ); ?>/simple-share-buttons-adder/images/simplesharebuttons.png" alt="Simple Share Buttons Plus" class="ssba-logo-img" />
				</a>
			</div>
		</div>
	</nav>

	<div class="modal fade" id="ssbaSupportModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><?php echo esc_html__( 'Simple Share Buttons Support', 'simple-share-buttons-adder' ); ?></h4>
				</div>
				<div class="modal-body">
					<p><?php echo esc_html__( 'Please note that the this plugin relies mostly on WordPress community support from other  users.', 'simple-share-buttons-adder' ); ?></p>
					<p><?php echo esc_html__( 'If you wish to receive official support, please consider purchasing', 'simple-share-buttons-adder' ); ?> <a href="https://simplesharebuttons.com/plus/?utm_source=adder&utm_medium=plugin_ad&utm_campaign=product&utm_content=support_modal" target="_blank"><b><?php echo esc_html__( 'Simple Share Buttons Plus', 'simple-share-buttons-adder' ); ?></b></a></p>
					<div class="row">
						<div class="col-sm-6">
							<a href="https://wordpress.org/support/plugin/simple-share-buttons-adder" target="_blank">
								<button class="btn btn-block btn-default"><?php echo esc_html__( 'Community support', 'simple-share-buttons-adder' ); ?></button>
							</a>
						</div>
						<div class="col-sm-6">
							<a href="https://simplesharebuttons.com/plus/?utm_source=adder&utm_medium=plugin_ad&utm_campaign=product&utm_content=support_modal" target="_blank">
								<button class="btn btn-block btn-primary"><?php echo esc_html__( 'Check out Plus', 'simple-share-buttons-adder' ); ?></button>
							</a>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo esc_html__( 'Close', 'simple-share-buttons-adder' ); ?></button>
				</div>
			</div>
		</div>
	</div>
	<div class="container">
