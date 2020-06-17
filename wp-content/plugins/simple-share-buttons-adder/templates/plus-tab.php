<?php
/**
 * Plus tab template.
 *
 * The template wrapper for the plus tab.
 *
 * @package SimpleShareButtonsAdder
 */

?>
<div class="tab-pane fade <?php echo 'active' === $modern ? esc_attr( $modern . ' in' ): ''; ?>" id="plus-share-buttons">
	<div class="col-sm-12 ssba-tab-container">
		<?php echo $this->forms->ssbp_input( $opts43 ); // WPCS: XSS ok. ?>

		<blockquote>
			<p>
				<?php echo esc_html__( 'Modern Share Buttons are CSS-based and allow for Button, Icon, and Icon Hover color customization. You can use our predefined CSS themes or your own custom CSS.' ); ?>
			</p>
		</blockquote>
		<blockquote>
			<p>
				<?php echo esc_html__( 'Activating Modern Share Buttons will override your Classic Share Buttons. None of your classic settings will be lost. If you wish to switch back to classic buttons simply deactivate the Modern Share Buttons. Use the simple settings below to get started.  Once you’re done you can further customize the share buttons via the options in the styling, counters, advanced, and CSS sections located below the preview.' ); ?>
			</p>
		</blockquote>

		<label for="ssba_choices" class="control-label" data-toggle="tooltip" data-placement="right" data-original-title="<?php echo esc_attr__( 'Drag, drop and reorder those buttons that you wish to include', 'simple-share-buttons-adder' ); ?>"><?php echo esc_html__( 'Networks', 'simple-share-buttons-adder' ); ?></label>

		<div>
		<div class="ssbp-wrap ssbp--centred ssbp--theme-4">
			<div class="ssbp-container">
				<ul id="ssbasort5" class="ssbp-list ssbaSortable">
					<?php echo wp_kses_post( $this->get_available_ssba( $arr_settings['ssba_selected_plus_buttons'], $arr_settings ) ); ?>
				</ul>
			</div>
		</div>
		</div>
		<div class="well">
			<div class="ssba-well-instruction">
				<i class="fa fa-download"></i> <?php echo esc_html__( 'Drop icons below', 'simple-share-buttons-adder' ); ?>
			</div>
			<div class="ssbp-wrap ssbp--centred ssbp--theme-4">
				<div class="ssbp-container">
					<ul id="ssbasort6" class="ssba-include-list ssbp-list ssbaSortable">
						<?php echo wp_kses_post( $this->get_selected_ssba( $arr_settings['ssba_selected_plus_buttons'], $arr_settings ) ); ?>
					</ul>
				</div>
			</div>
		</div>

		<?php if ( in_array( 'whatsapp', explode( ',', $arr_settings['ssba_selected_plus_buttons'] ), true ) ) : ?>
			<div class="ssbp--theme-4 whatsapp-message">
				<span class="ssbp-btn ssbp-whatsapp"></span>
				<?php echo esc_html__( 'The whatsapp button only appears on mobile devices. It is included in your desktop preview for reference only.', 'simple-share-buttons-adder' ); ?>
			</div>
		<?php endif; ?>

		<input type="hidden" name="ssba_selected_plus_buttons" id="ssba_selected_plus_buttons" value="<?php esc_attr( $arr_settings['ssba_selected_plus_buttons'] ); ?>"/>

		<?php
		echo $this->forms->ssbp_checkboxes( $opts48 ); // WPCS: XSS ok.
		echo $this->forms->ssbp_input( $opts49 ); // WPCS: XSS ok.
		echo $this->forms->ssbp_input( $page_omit_plus ); // WPCS: XSS ok.
		?>

		<h3 id="ssba-preview-title-2"><?php echo esc_html__( 'Preview - the order of your preview will update when you save.', 'simple-share-buttons-adder' ); ?></h3>

		<div class="master-ssba-prev-wrap2">
			<div id="ssba-preview" style="<?php echo esc_attr( 'text-align: ' . $arr_settings['ssba_plus_align'] . ';' ); ?>" class="<?php echo isset( $arr_settings['ssba_plus_position'] ) ? esc_attr( $arr_settings['ssba_plus_position'] ) : ''; ?> ssbp-wrap ssbp--theme-<?php echo esc_attr( $arr_settings['ssba_plus_button_style'] ); ?>">
				<div class="ssba-preview-content ssbp-container">
					<div style="position: relative; display: <?php echo esc_attr( 'below' === $arr_settings['ssba_plus_text_placement'] ? 'table-footer-group' : '' ); ?>; float: <?php echo esc_attr( 'above' === $arr_settings['ssba_plus_text_placement'] ? 'none' : $arr_settings['ssba_plus_text_placement'] ); ?>; color: <?php echo esc_attr( $arr_settings['ssba_plus_font_color'] ); ?>; font-family: <?php echo esc_attr( $arr_settings['ssba_plus_font_family'] ); ?>; font-weight: <?php echo esc_attr( $arr_settings['ssba_plus_font_weight'] ); ?>; font-size: <?php echo esc_attr( $arr_settings['ssba_plus_font_size'] ); ?>px;" class="ssba-share-text-prev">
						<?php echo esc_html( $arr_settings['ssba_plus_share_text'] ); ?>
					</div>

					<ul class="ssbp-list">
						<?php if ( is_array( $arr_plus_buttons ) ) : ?>
						<?php foreach ( $arr_plus_buttons as $buttons ) :
							$button = strtolower( str_replace( ' ', '_', str_replace( '+', '', $buttons['full_name'] ) ) ); ?>

							<li style="margin-left: <?php echo esc_attr( $arr_settings['ssba_plus_margin'] ); ?>px;" class="ssbp-li--<?php echo esc_attr( $button );
							if ( ! in_array( $button, explode( ',', $arr_settings['ssba_selected_plus_buttons'] ), true ) ) {
								echo esc_attr( ' ssba-hide-button' );
							}
							?>">
							<?php if ( 'facebook_save' !== $button ) : ?>
								<a href="#" class="ssbp-btn ssbp-<?php echo esc_attr( $button ); ?>" style="height: <?php echo esc_attr( $arr_settings['ssba_plus_height'] ); ?>px; width: <?php echo esc_attr( $arr_settings['ssba_plus_width'] ); ?>px; <?php echo '' !== $arr_settings['ssba_plus_button_color'] ? esc_attr( 'background: ' . $arr_settings['ssba_plus_button_color'] . ';' ) : ''; ?>"><div title="<?php echo esc_attr( $buttons['full_name'] ); ?>" class="ssbp-text"><?php echo esc_html( $buttons['full_name'] ); ?></div></a>
								<span class="<?php echo 'Y' !== $arr_settings['ssba_plus_show_share_count'] ? esc_attr( 'ssba-hide-button' ) : ''; ?> ssbp-each-share">1.8k</span>
							<?php else :
								$img_src = esc_attr( $this->plugin->dir_url ) . 'buttons/' . esc_attr( $arr_settings['ssba_image_set'] ) . '/facebook_save.png';
								?>
								<div style="display:none;" title="<?php echo esc_attr( $buttons['full_name'] ); ?>" class="ssbp-text"><?php echo esc_html( $buttons['full_name'] ); ?></div>
								<img style="height: 35px; padding: <?php echo esc_attr( $arr_settings['ssba_plus_margin'] ); ?>px;" src="<?php echo esc_attr( $img_src ); ?>" title="<?php echo esc_attr( $buttons['full_name'] ); ?>" class="ssba ssba-img" alt="Share on <?php echo esc_attr( $button ); ?>" />
							<?php endif; ?>
							</li>
						<?php endforeach; ?>
						<?php endif; ?>
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
						<?php echo $this->forms->ssbp_input( $opts44 ); // WPCS: XSS ok. ?>
					</div>
					<div class="col-md-6">
						<?php echo $this->forms->ssbp_input( $opts7p ); // WPCS: XSS ok. ?>
					</div>

					<div class="col-md-12">
						<h3><?php echo esc_html__( 'Size', 'simple-share-buttons-adder' ); ?></h3>
					</div>

					<div class="col-md-6">
						<?php echo $this->forms->ssbp_input( $plus_height ); // WPCS: XSS ok. ?>
						<?php echo $this->forms->ssbp_input( $plus_width ); // WPCS: XSS ok. ?>
					</div>
					<div class="col-md-6">
						<?php echo $this->forms->ssbp_input( $plus_icon_size ); // WPCS: XSS ok. ?>
						<?php echo $this->forms->ssbp_input( $plus_margin ); // WPCS: XSS ok. ?>
					</div>

					<div class="col-md-12">
						<h3><?php echo esc_html__( 'Color Overrides', 'simple-share-buttons-adder' ); ?></h3>
					</div>

					<div class="col-md-6">
						<?php echo $this->forms->ssbp_input( $plus_button_color ); // WPCS: XSS ok. ?>
						<?php echo $this->forms->ssbp_input( $plus_hover_color ); // WPCS: XSS ok. ?>
					</div>
					<div class="col-md-6">
						<?php echo $this->forms->ssbp_input( $plus_icon_color ); // WPCS: XSS ok. ?>
						<?php echo $this->forms->ssbp_input( $plus_icon_hover_color ); // WPCS: XSS ok. ?>
					</div>

					<div class="col-md-12">
						<h3><?php echo esc_html__( 'Share Text', 'simple-share-buttons-adder' ); ?></h3>
					</div>
					<div class="col-md-6 share-text-prev">
						<?php
						echo $this->forms->ssbp_input( $opts3p ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts10p ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts11p ); // WPCS: XSS ok.
						?>
					</div>
					<div class="col-md-6 share-text-prev">
						<?php
						echo $this->forms->ssbp_input( $opts12p ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts13p ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts9p ); // WPCS: XSS ok.
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
						echo $this->forms->ssbp_input( $opts19p ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts20p ); // WPCS: XSS ok.
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
						echo $this->forms->ssbp_input( $opts26p ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts28p ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts29p ); // WPCS: XSS ok.
						?>
					</div>

					<div class="col-md-6">
						<?php echo $this->forms->ssbp_input( $opts30p ); // WPCS: XSS ok. ?>
					</div>

					<div class="col-md-6">
						<?php echo $this->forms->ssbp_input( $opts31p ); // WPCS: XSS ok. ?>
					</div>

					<div class="col-md-12">
						<?php echo esc_html__( 'You shall need to follow the instructions here before enabling this feature', 'simple-share-buttons-adder' ); ?> - <a target="_blank" href="https://developers.facebook.com/docs/apps/register"><?php echo esc_html( 'https://developers.facebook.com/docs/apps/register' ); ?></a>
					</div>

					<div class="col-md-12">
						<?php echo $this->forms->ssbp_input( $opts33p ); // WPCS: XSS ok.?>
					</div>

					<div class="col-md-12">
						<?php echo $this->forms->ssbp_input( $plus_ignore_sdk ); // WPCS: XSS ok.?>
					</div>

					<div class="col-md-12">
						<?php echo esc_html__( 'You shall need have created and added a Facebook App ID above to make use of this feature', 'simple-share-buttons-adder' ); ?>
					</div>

					<div class="col-md-12">
						<?php echo $this->forms->ssbp_input( $opts32p ); // WPCS: XSS ok. ?>
					</div>

					<div class="col-md-6">
						<?php
						echo $this->forms->ssbp_input( $opts34p ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts37p ); // WPCS: XSS ok.
						?>
					</div>
					<div class="col-md-6">
						<?php
						echo $this->forms->ssbp_input( $opts35p ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts36p ); // WPCS: XSS ok.
						?>
					</div>

					<div class="col-md-12">
						<?php
						echo $this->forms->ssbp_input( $opts38p ); // WPCS: XSS ok.
						echo $this->forms->ssbp_input( $opts39p ); // WPCS: XSS ok.
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
						<?php echo $this->forms->ssbp_input( $opts40p ); // WPCS: XSS ok. ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
