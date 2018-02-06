<?php
/**
 * Classic tab template.
 *
 * The template wrapper for the classic tab.
 *
 * @package SimpleShareButtonsAdder
 */

?>
<div class="tab-pane fade active in" id="classic-share-buttons">
	<div class="col-sm-12 ssba-tab-container">
		<?php if ( ! isset( $notice['new-tab-notice'] ) ) : ?>
			<blockquote class="yellow">
				<p>
					<?php echo esc_html__( 'All of the plugin settings are now included on this page. No more switching tabs! Scroll down past the preview to access the styling, counters, advanced and css settings.', 'simple-share-buttons-adder' ); ?>

					<button id="new-tab-notice" type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
				</p>
			</blockquote>
		<?php endif; ?>

		<blockquote>
			<p>
				<?php echo esc_html__( 'The', 'simple-share-buttons-adder' ); ?> <b><?php echo esc_html__( 'simple', 'simple-share-buttons-adder' ); ?></b> <?php echo esc_html__( 'options you can see below are all you need to complete to get your', 'simple-share-buttons-adder' ); ?> <b><?php echo esc_html__( 'share buttons', 'simple-share-buttons-adder' ); ?></b> <?php echo esc_html__( 'to appear on your website. Once you\'re done here, you can further customise the share buttons via the Styling tab.', 'simple-share-buttons-adder' ); ?>
			</p>
		</blockquote>

		<label for="ssba_choices" class="control-label" data-toggle="tooltip" data-placement="right" data-original-title="<?php echo esc_attr__( 'Drag, drop and reorder those buttons that you wish to include', 'simple-share-buttons-adder' ); ?>"><?php echo esc_html__( 'Networks', 'simple-share-buttons-adder' ); ?></label>

		<div>
			<div class="ssbp-wrap ssbp--centred ssbp--theme-4">
				<div class="ssbp-container">
					<ul id="ssbasort1" class="ssbp-list ssbaSortable">
						<?php echo wp_kses_post( $this->get_available_ssba( $arr_settings['ssba_selected_buttons'], $arr_settings ) ); ?>
					</ul>
				</div>
			</div>
			<div class="well">
				<div class="ssba-well-instruction">
					<i class="fa fa-download"></i> <?php echo esc_html__( 'Drop icons below - the order of your preview will update when you save.', 'simple-share-buttons-adder' ); ?>
				</div>
				<div class="ssbp-wrap ssbp--centred ssbp--theme-4">
					<div class="ssbp-container">
						<ul id="ssbasort2" class="ssba-include-list ssbp-list ssbaSortable">
							<?php echo wp_kses_post( $this->get_selected_ssba( $arr_settings['ssba_selected_buttons'], $arr_settings ) ); ?>
						</ul>
					</div>
				</div>
			</div>

			<?php if ( in_array( 'whatsapp', explode( ',', $arr_settings['ssba_selected_buttons'] ), true ) ) : ?>
				<div class="ssbp--theme-4 whatsapp-message">
					<span class="ssbp-btn ssbp-whatsapp"></span>
					<?php echo esc_html__( 'The whatsapp button only appears on mobile devices. It is included in your desktop preview for reference only.', 'prodigy-commerce' ); ?>
				</div>
			<?php endif; ?>

			<input type="hidden" name="ssba_selected_buttons" id="ssba_selected_buttons" value="<?php esc_attr( $arr_settings['ssba_selected_buttons'] ); ?>"/>
		</div>
		<?php
		echo $this->forms->ssbp_checkboxes( $opts1 ); // WPCS: XSS ok.
		echo $this->forms->ssbp_input( $opts2 ); // WPCS: XSS ok.

		$line_height = 'below' === $arr_settings['ssba_text_placement'] || 'above' === $arr_settings['ssba_text_placement'] ? 'inherit' : ( (int) $arr_settings['ssba_size'] + (int) $arr_settings['ssba_padding'] + 3 ) . 'px';
		$image_line_height = $arr_settings['ssba_size'] . 'px';
		?>

		<h3 id="ssba-preview-title"><?php echo esc_html__( 'Preview', 'simple-share-buttons-adder' ); ?></h3>

		<div class="master-ssba-prev-wrap">
			<div id="ssba-preview-1" class="ssbp-wrap" style="text-align: <?php echo esc_attr( $arr_settings['ssba_align'] ); ?>; border-radius: <?php echo 'Y' === $arr_settings['ssba_div_rounded_corners'] ? esc_attr( '10px' ) : esc_attr( '0' ); ?>; border: <?php echo esc_attr( $arr_settings['ssba_border_width'] . 'px solid ' . $arr_settings['ssba_div_border'] ); ?>; background: <?php echo esc_attr( $arr_settings['ssba_div_background'] ); ?>; padding: <?php echo esc_attr( $arr_settings['ssba_div_padding'] ); ?>px;">
				<div class="ssba-preview-content">
					<div style="display: <?php echo esc_attr( 'below' === $arr_settings['ssba_text_placement'] ? 'table-footer-group' : '' ); ?>; line-height: <?php echo esc_attr( $line_height ); ?>; float: <?php echo esc_attr( 'above' === $arr_settings['ssba_text_placement'] ? 'none' : $arr_settings['ssba_text_placement'] ); ?>; color: <?php echo esc_attr( $arr_settings['ssba_font_color'] ); ?>; font-family: <?php echo esc_attr( $arr_settings['ssba_font_family'] ); ?>; font-weight: <?php echo esc_attr( $arr_settings['ssba_font_weight'] ); ?>; font-size: <?php echo esc_attr( $arr_settings['ssba_font_size'] ); ?>px;" class="ssba-share-text-prev">
						<?php echo esc_html( $arr_settings['ssba_share_text'] ); ?>
					</div>

					<ul class="ssbp-list">
						<?php foreach ( $arr_buttons as $buttons ) :
							$button = strtolower( str_replace( ' ', '_', str_replace( '+', '', $buttons['full_name'] ) ) );

							if ( 'custom' !== $arr_settings['ssba_image_set'] ) {
								$img_src = esc_attr( $this->plugin->dir_url ) . 'buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/' . esc_attr( $button ) . '.png';
							} else {
								$img_src = isset( $custom_buttons[ $button ] ) ? $custom_buttons[ $button ] : '';
							} ?>
							<li class="ssbp-li--<?php echo esc_attr( $button );
							if ( ! in_array( $button, explode( ',', $arr_settings['ssba_selected_buttons'] ), true ) ) {
								echo esc_attr( ' ssba-hide-button' );
							}
							?>">
								<img style="line-height: <?php echo esc_attr( $image_line_height ); ?>; height: <?php echo esc_attr( $arr_settings['ssba_size'] ); ?>px; padding: <?php echo esc_attr( $arr_settings['ssba_padding'] ); ?>px;" src="<?php echo esc_attr( $img_src ); ?>" title="<?php echo esc_attr( $buttons['full_name'] ); ?>" class="ssba ssba-img" alt="Share on <?php echo esc_attr( $button ); ?>" />
								<?php if ( 'facebook_save' !== $button ) : ?>
									<span style="vertical-align: middle;" class="<?php echo 'Y' === $arr_settings['ssba_show_share_count'] ? esc_attr( 'ssba_sharecount ssba_' . $arr_settings['ssba_share_count_style'] ) : ''; ?> ssbp-total-<?php echo esc_attr( $button ); ?>-shares">1.8k</span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
		<div class="accor-wrap">
			<div class="accor-tab">
				<span class="accor-arrow">&#9658;</span>
				<?php echo esc_html__( 'Styling', 'simple-share-buttons-adder' ); ?>
			</div>
			<div class="accor-content">
				<div class="well">
					<div class="col-md-12">
						<h3><?php echo esc_html__( 'Appearance', 'simple-share-buttons-adder' ); ?></h3>
					</div>

					<div class="col-md-6">
						<?php echo $this->forms->ssbp_input( $opts4 ); // WPCS: XSS ok.?>

						<div id="ssba-custom-images" <?php echo 'custom' !== $arr_settings['ssba_image_set'] ? 'style="display: none;"' : null; ?>>
							<?php
							// Loop through each button.
							foreach ( $arr_buttons as $button => $arr_button ) {
								$custom_button = strtolower( str_replace( ' ', '_', str_replace( '+', '', $arr_button['full_name'] ) ) );

								// Enable custom images.
								$opts5                 = array(
									'form_group' => false,
									'type'       => 'image_upload',
									'name'       => 'ssba_custom_' . $custom_button,
									'label'      => $arr_button['full_name'],
									'tooltip'    => 'Upload a custom ' . $arr_button['full_name'] . ' image',
									'value'      => isset( $arr_settings[ 'ssba_custom_' . $custom_button ] ) ? $arr_settings[ 'ssba_custom_' . $custom_button ] : '',
								);
								echo $this->forms->ssbp_input( $opts5 ); // WPCS: XSS ok.
							}
							?>
						</div>

						<?php echo $this->forms->ssbp_input( $opts6 ); // WPCS: XSS ok. ?>
					</div>
					<div class="col-md-6">
						<?php
						echo $this->forms->ssbp_input( $opts7 ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts8 ); // WPCS: XSS ok.
						?>
					</div>

					<div class="col-md-12">
						<h3><?php echo esc_html__( 'Share Text', 'simple-share-buttons-adder' ); ?></h3>
					</div>
					<div class="col-md-6 share-text-prev">
						<?php
						echo $this->forms->ssbp_input( $opts3 ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts10 ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts11 ); // WPCS: XSS ok.
						?>
					</div>
					<div class="col-md-6 share-text-prev">
						<?php
						echo $this->forms->ssbp_input( $opts12 ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts13 ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts9 ); // WPCS: XSS ok.
						?>
					</div>

					<div class="col-md-12">
						<h3><?php echo esc_html__( 'Container', 'simple-share-buttons-adder' ); ?></h3>
					</div>

					<div class="col-md-12 share-cont-prev">
						<?php echo $this->forms->ssbp_input( $opts18 ); // WPCS: XSS ok. ?>
					</div>

					<div class="col-md-6 share-cont-prev">
						<?php
						echo $this->forms->ssbp_input( $opts14 ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts17 ); // WPCS: XSS ok.
						?>
					</div>
					<div class="col-md-6 share-cont-prev">
						<?php
						echo $this->forms->ssbp_input( $opts16 ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts15 ); // WPCS: XSS ok.
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="accor-wrap">
			<div class="accor-tab">
				<span class="accor-arrow">&#9658;</span>
				<?php echo esc_html__( 'Counters', 'simple-share-buttons-adder' ); ?>
			</div>
			<div class="accor-content">
				<div class="well">
					<div class="col-md-12">
						<h3><?php echo esc_html__( 'Share Counts', 'simple-share-buttons-adder' ); ?></h3>
					</div>

					<div class="col-md-12 share-count-prev">
						<?php
						echo $this->forms->ssbp_input( $opts19 ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts20 ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts21 ); // WPCS: XSS ok.
						?>

						<p>
							<strong>
								<?php echo esc_html( 'newsharecounts.com Counts for Twitter', 'simple-share-buttons-adder' ); ?>
							</strong>
							<br>
							<?php echo esc_html__( 'You shall need to follow the instructions here before enabling this feature', 'simple-share-buttons-adder' ); ?> - <a target="_blank" href="http://newsharecounts.com/">newsharecounts.com</a>
							<?php echo $this->forms->ssbp_input( $opts22 ); // WPCS: XSS ok. ?>
						</p>

						<h3>sharedcount.com</h3>
						<p>
							<?php echo esc_html__( 'Only necessary if you are experiencing issues with Facebook share counts.', 'simple-share-buttons-adder' ); ?> <a href="https://admin.sharedcount.com/admin/signup.php" target="_blank"><?php echo esc_html__( 'Signup for your free account here', 'simple-share-buttons-adder' ); ?></a>.
						</p>

						<?php echo $this->forms->ssbp_input( $opts23 ); // WPCS: XSS ok. ?>
					</div>
					<div class="col-md-6">
						<?php echo $this->forms->ssbp_input( $opts24 ); // WPCS: XSS ok. ?>
					</div>
					<div class="col-md-6">
						<?php echo $this->forms->ssbp_input( $opts25 ); // WPCS: XSS ok. ?>
					</div>
				</div>
			</div>
		</div>

		<div class="accor-wrap">
			<div class="accor-tab">
				<span class="accor-arrow">&#9658;</span>
				<?php echo esc_html__( 'Shortcode', 'simple-share-buttons-adder' ); ?>
			</div>
			<div class="accor-content">
				<div class="well">
					<div class="col-md-12 text-center">
						Use the shortcode below to insert your buttons in pages and posts.
						<p>
							<textarea id="holdtext" style="display:none;"></textarea>
							<input type="text" class="form-control ssba-buttons-shortcode" value="<?php echo esc_attr( '[ssba-buttons]' ); ?>" readonly size="40"/>
							<button class="input-group-addon" type="button" id="ssba-copy-shortcode"><?php esc_html_e( 'Copy', 'simple-share-buttons-adder' ); ?></button>
						</p>
					</div>
				</div>
			</div>
		</div>

		<div class="accor-wrap">
			<div class="accor-tab">
				<span class="accor-arrow">&#9658;</span>
				<?php echo esc_html__( 'Advanced', 'simple-share-buttons-adder' ); ?>
			</div>
			<div class="accor-content">
				<div class="well">
					<div class="col-md-12">
						<h3><?php echo esc_html__( 'Advanced functionality', 'simple-share-buttons-adder' ); ?></h3>
					</div>

					<div class="col-md-12">
						<?php
						echo $this->forms->ssbp_input( $opts26 ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts27 ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts28 ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts29 ); // WPCS: XSS ok.
						?>
					</div>

					<div class="col-md-6">
						<?php echo $this->forms->ssbp_input( $opts30 ); // WPCS: XSS ok. ?>
					</div>

					<div class="col-md-6">
						<?php echo $this->forms->ssbp_input( $opts31 ); // WPCS: XSS ok. ?>
					</div>

					<div class="col-md-12">
						<?php echo esc_html__( 'You shall need to follow the instructions here before enabling this feature', 'simple-share-buttons-adder' ); ?> - <a target="_blank" href="https://developers.facebook.com/docs/apps/register"><?php echo esc_html( 'https://developers.facebook.com/docs/apps/register' ); ?></a>
					</div>

					<div class="col-md-12">
						<?php echo $this->forms->ssbp_input( $opts33 ); // WPCS: XSS ok.?>
					</div>

					<div class="col-md-12">
						<?php echo esc_html__( 'You shall need have created and added a Facebook App ID above to make use of this feature', 'simple-share-buttons-adder' ); ?>
					</div>

					<div class="col-md-12">
						<?php echo $this->forms->ssbp_input( $opts32 ); // WPCS: XSS ok. ?>
					</div>

					<div class="col-md-6">
						<?php
						echo $this->forms->ssbp_input( $opts34 ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts37 ); // WPCS: XSS ok.
						?>
					</div>
					<div class="col-md-6">
						<?php
						echo $this->forms->ssbp_input( $opts35 ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts36 ); // WPCS: XSS ok.
						?>
					</div>

					<div class="col-md-12">
						<?php
						echo $this->forms->ssbp_input( $opts38 ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts39 ); // WPCS: XSS ok.
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="accor-wrap">
			<div class="accor-tab">
				<span class="accor-arrow">&#9658;</span>
				<?php echo esc_html__( 'CSS', 'simple-share-buttons-adder' ); ?>
			</div>
			<div class="accor-content">
				<div class="well">
					<div class="col-md-12">
						<h3><?php echo esc_html__( 'CSS overrides', 'simple-share-buttons-adder' ); ?></h3>
					</div>

					<div class="col-md-12">
						<blockquote>
							<p><?php echo esc_html__( 'The contents of the text area below will be appended to Simple Share Button Adder\'s CSS.', 'simple-share-buttons-adder' ); ?></p>
						</blockquote>
					</div>

					<div class="col-sm-12">
						<?php echo $this->forms->ssbp_input( $opts40 ); // WPCS: XSS ok.?>
					</div>

					<div class="col-md-12">
						<blockquote>
							<p><?php echo esc_html__( 'If you want to take over control of your share buttons\' CSS entirely, turn on the switch below and enter your custom CSS.', 'simple-share-buttons-adder' ); ?> <strong><?php echo esc_html__( 'ALL of Simple Share Buttons Adder\'s CSS will be disabled', 'simple-share-buttons-adder' ); ?></strong>.</p>
						</blockquote>
					</div>

					<div class="col-sm-12">
						<?php
						echo $this->forms->ssbp_input( $opts41 ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts42 ); // WPCS: XSS ok.
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
