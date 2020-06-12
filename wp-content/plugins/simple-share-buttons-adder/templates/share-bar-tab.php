<?php
/**
 * Share bar tab template.
 *
 * The template wrapper for the share bar tab.
 *
 * @package SimpleShareButtonsAdder
 */

?>
<div class="tab-pane fade <?php echo 'active' === $bar ? esc_attr( $bar . ' in' ): ''; ?>" id="share-bar">
	<div class="col-sm-12 ssba-tab-container">
		<?php echo $this->forms->ssbp_input( $share_bar ); // WPCS: XSS ok. ?>

		<blockquote>
			<p>
				<?php echo esc_html__( 'The', 'simple-share-buttons-adder' ); ?> <b><?php echo esc_html__( 'simple', 'simple-share-buttons-adder' ); ?></b> <?php echo esc_html__( 'options you can see below are all you need to complete to get your', 'simple-share-buttons-adder' ); ?> <b><?php echo esc_html__( 'share buttons', 'simple-share-buttons-adder' ); ?></b> <?php echo esc_html__( 'to appear on your website. Once you\'re done here, you can further customise the share buttons via the Styling tab.', 'simple-share-buttons-adder' ); ?>
			</p>
		</blockquote>

		<label for="ssba_choices" class="control-label" data-toggle="tooltip" data-placement="right" data-original-title="<?php echo esc_attr__( 'Drag, drop and reorder those buttons that you wish to include', 'simple-share-buttons-adder' ); ?>"><?php echo esc_html__( 'Networks', 'simple-share-buttons-adder' ); ?></label>

		<div class="">
			<div class="ssbp-wrap ssbp--centred ssbp--theme-4">
				<div class="ssbp-container">
					<ul id="ssbasort3" class="ssbp-list ssbaSortable">
						<?php $arr_settings = array_merge( $arr_settings, array(
							'bar_call' => 'Y',
							)
						);

						echo wp_kses_post( $this->get_available_ssba( $arr_settings['ssba_selected_bar_buttons'], $arr_settings ) ); ?>
					</ul>
				</div>
			</div>
			<div class="well">
				<div class="ssba-well-instruction">
					<i class="fa fa-download"></i> <?php echo esc_html__( 'Drop icons below', 'simple-share-buttons-adder' ); ?>
				</div>
				<div class="ssbp-wrap ssbp--centred ssbp--theme-4">
					<div class="ssbp-container">
						<ul id="ssbasort4" class="ssba-include-list ssbp-list ssbaSortable">
							<?php echo wp_kses_post( $this->get_selected_ssba( $arr_settings['ssba_selected_bar_buttons'], $arr_settings ) ); ?>
						</ul>
					</div>
				</div>
			</div>

			<?php if ( in_array( 'whatsapp', explode( ',', $arr_settings['ssba_selected_bar_buttons'] ), true ) ) : ?>
				<div class="ssbp--theme-4 whatsapp-message">
					<span class="ssbp-btn ssbp-whatsapp"></span>
					<?php echo esc_html__( 'The whatsapp button only appears on mobile devices. It is included in your desktop preview for reference only.', 'simple-share-buttons-adder' ); ?>
				</div>
			<?php endif; ?>

			<input type="hidden" name="ssba_selected_bar_buttons" id="ssba_selected_bar_buttons" value="<?php esc_attr( $arr_settings['ssba_selected_bar_buttons'] ); ?>"/>
		</div>

		<div id="ssba-preview-2" class="<?php echo esc_attr( $arr_settings['ssba_bar_position'] ); ?> ssbp-wrap ssbp--theme-<?php echo esc_attr( $arr_settings['ssba_bar_style'] ); ?>">
			<div class="ssbp-container">
				<ul class="ssbp-list">
					<?php if ( is_array( $arr_bar_buttons ) ) : ?>
					<?php foreach ( $arr_bar_buttons as $buttons ) :
						$button = strtolower( str_replace( ' ', '_', str_replace( '+', '', $buttons['full_name'] ) ) ); ?>
						<li style="margin: <?php echo esc_attr( $arr_settings['ssba_bar_margin'] ); ?>px 0;" class="ssbp-li--<?php echo esc_attr( $button );
						if ( ! in_array( $button, explode( ',', $arr_settings['ssba_selected_bar_buttons'] ), true ) ) {
							echo esc_attr( ' ssba-hide-button' );
						}
						?>">
							<a href="#" class="ssbp-btn ssbp-<?php echo esc_attr( $button ); ?>" style="height: <?php echo esc_attr( $arr_settings['ssba_bar_height'] ); ?>px; width: <?php echo esc_attr( $arr_settings['ssba_bar_width'] ); ?>px; <?php echo '' !== $arr_settings['ssba_bar_button_color'] ? esc_attr( 'background: ' . $arr_settings['ssba_bar_button_color'] . ';' ) : ''; ?>">
								<div title="<?php echo esc_attr( $buttons['full_name'] ); ?>" class="ssbp-text">
									<?php echo esc_html( $buttons['full_name'] ); ?>
								</div>
							</a>
							<span class="<?php echo 'Y' !== $arr_settings['ssba_bar_show_share_count'] ? esc_attr( 'ssba-hide-button' ) : ''; ?> ssbp-each-share">1.8k</span>
						</li>
					<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</div>
		</div>
		<?php
		echo $this->forms->ssbp_checkboxes( $opts45 ); // WPCS: XSS ok.
		echo $this->forms->ssbp_checkboxes( $share_bar_display ); // WPCS: XSS ok.
		echo $this->forms->ssbp_input( $page_omit_bar ); // WPCS: XSS ok.
		?>
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
						<?php echo $this->forms->ssbp_input( $opts46 ); // WPCS: XSS ok. ?>
					</div>
					<div class="col-md-6">
						<?php echo $this->forms->ssbp_input( $opts47 ); // WPCS: XSS ok. ?>
					</div>

					<div class="col-md-12">
						<?php echo $this->forms->ssbp_input( $mobile_breakpoint ); // WPCS: XSS ok. ?>
					</div>

					<div class="col-md-12">
						<h3><?php echo esc_html__( 'Size', 'simple-share-buttons-adder' ); ?></h3>
					</div>

					<div class="col-md-6">
						<?php echo $this->forms->ssbp_input( $share_height ); // WPCS: XSS ok. ?>
						<?php echo $this->forms->ssbp_input( $share_width ); // WPCS: XSS ok. ?>
					</div>
					<div class="col-md-6">
						<?php echo $this->forms->ssbp_input( $share_icon_size ); // WPCS: XSS ok. ?>
						<?php echo $this->forms->ssbp_input( $share_margin ); // WPCS: XSS ok. ?>
					</div>

					<div class="col-md-12">
						<h3><?php echo esc_html__( 'Color Overrides', 'simple-share-buttons-adder' ); ?></h3>
					</div>

					<div class="col-md-6">
						<?php echo $this->forms->ssbp_input( $share_button_color ); // WPCS: XSS ok. ?>
						<?php echo $this->forms->ssbp_input( $share_hover_color ); // WPCS: XSS ok. ?>
					</div>
					<div class="col-md-6">
						<?php echo $this->forms->ssbp_input( $share_icon_color ); // WPCS: XSS ok. ?>
						<?php echo $this->forms->ssbp_input( $share_icon_hover_color ); // WPCS: XSS ok. ?>
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
						echo $this->forms->ssbp_input( $opts19s ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts20s ); // WPCS: XSS ok.
						?>
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
						echo $this->forms->ssbp_input( $opts28s ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts29s ); // WPCS: XSS ok.
						?>
					</div>

					<div class="col-md-12">
						<?php echo $this->forms->ssbp_input( $opts31s ); // WPCS: XSS ok. ?>
					</div>

					<div class="col-md-12">
						<?php echo esc_html__( 'You shall need to follow the instructions here before enabling this feature', 'simple-share-buttons-adder' ); ?> - <a target="_blank" href="https://developers.facebook.com/docs/apps/register"><?php echo esc_html( 'https://developers.facebook.com/docs/apps/register' ); ?></a>
					</div>

					<div class="col-md-12">
						<?php echo $this->forms->ssbp_input( $opts33s ); // WPCS: XSS ok.?>
					</div>

					<div class="col-md-12">
						<?php echo esc_html__( 'You shall need have created and added a Facebook App ID above to make use of this feature', 'simple-share-buttons-adder' ); ?>
					</div>

					<div class="col-md-12">
						<?php echo $this->forms->ssbp_input( $opts32s ); // WPCS: XSS ok. ?>
					</div>

					<div class="col-md-6">
						<?php
						echo $this->forms->ssbp_input( $opts34s ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts37s ); // WPCS: XSS ok.
						?>
					</div>
					<div class="col-md-6">
						<?php
						echo $this->forms->ssbp_input( $opts35s ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts36s ); // WPCS: XSS ok.
						?>
					</div>

					<div class="col-md-12">
						<?php
						echo $this->forms->ssbp_input( $opts38s ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts39s ); // WPCS: XSS ok.
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
						<?php echo $this->forms->ssbp_input( $opts40s ); // WPCS: XSS ok.?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
