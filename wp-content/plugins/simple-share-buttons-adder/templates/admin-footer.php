<?php
/**
 * Admin footer template.
 *
 * The template wrapper for the admin footer.
 *
 * @package SimpleShareButtonsAdder
 */

?>
		<footer class="row">
			<div class="col-sm-12">
				<a href="https://simplesharebuttons.com" target="_blank"><?php echo esc_html__( 'Simple Share Buttons Adder', 'simple-share-buttons-adder' ); ?></a>
				<span class="badge"><?php echo esc_html( SSBA_VERSION ); ?></span>
				<button type="button" class="ssba-btn-thank-you pull-right btn btn-primary" data-toggle="modal" data-target="#ssbaFooterModal">
					<i class="fa fa-info"></i>
				</button>
				<div class="modal fade" id="ssbaFooterModal" tabindex="-1" role="dialog" aria-labelledby="ssbaFooterModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
								<h4 class="modal-title"><?php echo esc_html__( 'Simple Share Buttons', 'simple-share-buttons-adder' ); ?></h4>
							</div>
							<div class="modal-body">
								<p><?php echo esc_html__( 'Many thanks for choosing', 'simple-share-buttons-adder' ); ?> <a href="https://simplesharebuttons.com" target="_blank"><?php echo esc_html__( 'Simple Share Buttons', 'simple-share-buttons-adder' ); ?></a> <?php echo esc_html__( 'for your share buttons plugin, we\'re confident you won\'t be disappointed in your decision. If you require any support, please visit the', 'simple-share-buttons-adder' ); ?> <a href="https://wordpress.org/support/plugin/simple-share-buttons-adder" target="_blank"><?php echo esc_html__( 'support forum', 'simple-share-buttons-adder' ); ?></a>.</p>
								<p><?php echo esc_html__( 'If you like the plugin, we\'d really appreciate it if you took a moment to', 'simple-share-buttons-adder' ); ?> <a href="https://wordpress.org/support/view/plugin-reviews/simple-share-buttons-adder" target="_blank"><?php echo esc_html__( 'leave a review', 'simple-share-buttons-adder' ); ?></a>, <?php echo esc_html__( 'if there\'s anything missing to get 5 stars do please', 'simple-share-buttons-adder' ); ?> <a href="https://simplesharebuttons.com/contact/" target="_blank"><?php echo esc_html__( 'let us know', 'simple-share-buttons-adder' ); ?></a>. <?php echo esc_html__( 'If you feel your website is worthy of appearing on our', 'simple-share-buttons-adder' ); ?> <a href="https://simplesharebuttons.com/showcase/" target="_blank"><?php echo esc_html__( 'showcase page', 'simple-share-buttons-adder' ); ?></a> do <a href="https://simplesharebuttons.com/contact/" target="_blank"><?php echo esc_html__( 'get in touch', 'simple-share-buttons-adder' ); ?></a>.</p>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo esc_html__( 'Close', 'simple-share-buttons-adder' ); ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</footer>
	</div>
</div>
