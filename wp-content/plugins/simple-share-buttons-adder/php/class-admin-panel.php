<?php
/**
 * Admin Panel.
 *
 * @package SimpleShareButtonsAdder
 */

namespace SimpleShareButtonsAdder;

/**
 * Admin Panel Class
 *
 * @package SimpleShareButtonsAdder
 */
class Admin_Panel {

	/**
	 * Plugin instance.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Simple Share Buttons Adder instance.
	 *
	 * @var object
	 */
	public $class_ssba;

	/**
	 * Forms instance.
	 *
	 * @var object
	 */
	public $forms;

	/**
	 * Admin_Panel constructor.
	 *
	 * @param object $plugin Plugin instance.
	 * @param object $class_ssba Simple Share Buttons Adder instance.
	 * @param object $forms Forms instance.
	 */
	public function __construct( $plugin, $class_ssba, $forms ) {
		$this->plugin = $plugin;
		$this->class_ssba = $class_ssba;
		$this->forms = $forms;
	}

	/**
	 * Display the admin header.
	 */
	public function admin_header() {
		include_once( "{$this->plugin->dir_path}/templates/admin-header.php" );
	}

	/**
	 * Display the admin footer.
	 */
	public function admin_footer() {
		include_once( "{$this->plugin->dir_path}/templates/admin-footer.php" );
	}

	/**
	 * Build the Admin Panel html variables and include template.
	 *
	 * @param array $arr_settings The current ssba settings.
	 */
	public function admin_panel( $arr_settings ) {
		// Locations array.
		$locs = array(
			'Homepage' => array(
				'value'   => 'ssba_homepage',
				'checked' => isset( $arr_settings['ssba_homepage'] ) && 'Y' === $arr_settings['ssba_homepage'] ? true : false,
			),
			'Pages' => array(
				'value'   => 'ssba_pages',
				'checked' => isset( $arr_settings['ssba_pages'] ) && 'Y' === $arr_settings['ssba_pages'] ? true : false,
			),
			'Posts' => array(
				'value'   => 'ssba_posts',
				'checked' => isset( $arr_settings['ssba_posts'] ) && 'Y' === $arr_settings['ssba_posts'] ? true : false,
			),
			'Excerpts' => array(
				'value'   => 'ssba_excerpts',
				'checked' => isset( $arr_settings['ssba_excerpts'] ) && 'Y' === $arr_settings['ssba_excerpts'] ? true : false,
			),
			'Categories/Archives' => array(
				'value'   => 'ssba_cats_archs',
				'checked' => isset( $arr_settings['ssba_cats_archs'] ) && 'Y' === $arr_settings['ssba_cats_archs'] ? true : false,
			),
		);

		// Locations array.
		$locs2 = array(
			'Homepage' => array(
				'value'   => 'ssba_bar_homepage',
				'checked' => isset( $arr_settings['ssba_bar_homepage'] ) && 'Y' === $arr_settings['ssba_bar_homepage'] ? true : false,
			),
			'Pages' => array(
				'value'   => 'ssba_bar_pages',
				'checked' => isset( $arr_settings['ssba_bar_pages'] ) && 'Y' === $arr_settings['ssba_bar_pages'] ? true : false,
			),
			'Posts' => array(
				'value'   => 'ssba_bar_posts',
				'checked' => isset( $arr_settings['ssba_bar_posts'] ) && 'Y' === $arr_settings['ssba_bar_posts'] ? true : false,
			),
			'Categories/Archives' => array(
				'value'   => 'ssba_bar_cats_archs',
				'checked' => isset( $arr_settings['ssba_bar_cats_archs'] ) && 'Y' === $arr_settings['ssba_bar_cats_archs'] ? true : false,
			),
		);

		// Locations array for plus.
		$locs3 = array(
			'Homepage' => array(
				'value'   => 'ssba_plus_homepage',
				'checked' => isset( $arr_settings['ssba_plus_homepage'] ) && 'Y' === $arr_settings['ssba_plus_homepage'] ? true : false,
			),
			'Pages' => array(
				'value'   => 'ssba_plus_pages',
				'checked' => isset( $arr_settings['ssba_plus_pages'] ) && 'Y' === $arr_settings['ssba_plus_pages'] ? true : false,
			),
			'Posts' => array(
				'value'   => 'ssba_plus_posts',
				'checked' => isset( $arr_settings['ssba_plus_posts'] ) && 'Y' === $arr_settings['ssba_plus_posts'] ? true : false,
			),
			'Excerpts' => array(
				'value'   => 'ssba_plus_excerpts',
				'checked' => isset( $arr_settings['ssba_plus_excerpts'] ) && 'Y' === $arr_settings['ssba_plus_excerpts'] ? true : false,
			),
			'Categories/Archives' => array(
				'value'   => 'ssba_plus_cats_archs',
				'checked' => isset( $arr_settings['ssba_plus_cats_archs'] ) && 'Y' === $arr_settings['ssba_plus_cats_archs'] ? true : false,
			),
		);

		// Display options.
		$display_loc = array(
			'Desktop' => array(
				'value'   => 'ssba_share_desktop',
				'checked' => isset( $arr_settings['ssba_share_desktop'] ) && 'Y' === $arr_settings['ssba_share_desktop'] ? true : false,
			),
			'Mobile' => array(
				'value'   => 'ssba_share_mobile',
				'checked' => isset( $arr_settings['ssba_share_mobile'] ) && 'Y' === $arr_settings['ssba_share_mobile'] ? true : false,
			),
		);

		// Prepare array of buttons.
		$arr_buttons = json_decode( get_option( 'ssba_buttons' ), true );

		// Locations.
		$opts1 = array(
			'form_group' => false,
			'label'      => 'Locations',
			'tooltip'    => 'Enable the locations you wish for share buttons to appear',
			'value'      => 'Y',
			'checkboxes' => $locs,
		);

		// Placement.
		$opts2 = array(
			'form_group' => false,
			'type'       => 'select',
			'name'       => 'ssba_before_or_after',
			'label'      => 'Placement',
			'tooltip'    => 'Place share buttons before or after your content',
			'selected'   => isset( $arr_settings['ssba_before_or_after'] ) ? $arr_settings['ssba_before_or_after'] : '',
			'options'    => array(
				'After'  => 'after',
				'Before' => 'before',
				'Both'   => 'both',
			),
		);

		// Share text.
		$opts3 = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'Share this...',
			'name'        => 'ssba_share_text',
			'label'       => 'Call To Action',
			'tooltip'     => 'Add some custom text by your share buttons',
			'value'       => isset( $arr_settings['ssba_share_text'] ) ? $arr_settings['ssba_share_text'] : '',
		);

		// Share text for plus.
		$opts3p = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'Share this...',
			'name'        => 'ssba_plus_share_text',
			'label'       => 'Call To Action',
			'tooltip'     => 'Add some custom text by your share buttons',
			'value'       => isset( $arr_settings['ssba_plus_share_text'] ) ? $arr_settings['ssba_plus_share_text'] : '',
		);

		// Placement.
		$opts4 = array(
			'form_group' => false,
			'type'       => 'select',
			'name'       => 'ssba_image_set',
			'label'      => 'Theme',
			'tooltip'    => 'Choose your favourite set of buttons, or set to custom to choose your own',
			'selected'   => isset( $arr_settings['ssba_image_set'] ) ? $arr_settings['ssba_image_set'] : '',
			'options'    => array(
				'Arbenta'  => 'arbenta',
				'Custom'   => 'custom',
				'Metal'    => 'metal',
				'Pagepeel' => 'pagepeel',
				'Plain'    => 'plain',
				'Retro'    => 'retro',
				'Ribbons'  => 'ribbons',
				'Simple'   => 'simple',
				'Somacro'  => 'somacro',
			),
		);

		// Button size.
		$opts6 = array(
			'form_group'  => false,
			'type'        => 'number_addon',
			'addon'       => 'px',
			'placeholder' => '35',
			'name'        => 'ssba_size',
			'label'       => 'Button Size',
			'tooltip'     => 'Set the size of your buttons in pixels',
			'value'       => isset( $arr_settings['ssba_size'] ) ? $arr_settings['ssba_size'] : '',
		);

		// Alignment.
		$opts7 = array(
			'form_group' => false,
			'type'       => 'select',
			'name'       => 'ssba_align',
			'label'      => 'Alignment',
			'tooltip'    => 'Align your buttons the way you wish',
			'selected'   => isset( $arr_settings['ssba_align'] ) ? $arr_settings['ssba_align'] : '',
			'options'    => array(
				'Left'   => 'left',
				'Center' => 'center',
				'Right'  => 'right',
			),
		);

		// Alignment for Plus.
		$opts7p = array(
			'form_group' => false,
			'type'       => 'select',
			'name'       => 'ssba_plus_align',
			'label'      => 'Alignment',
			'tooltip'    => 'Align your plus buttons the way you wish',
			'selected'   => isset( $arr_settings['ssba_plus_align'] ) ? $arr_settings['ssba_plus_align'] : '',
			'options'    => array(
				'Left'   => 'left',
				'Center' => 'center',
				'Right'  => 'right',
			),
		);

		// Padding.
		$opts8 = array(
			'form_group'  => false,
			'type'        => 'number_addon',
			'addon'       => 'px',
			'placeholder' => '10',
			'name'        => 'ssba_padding',
			'label'       => 'Padding',
			'tooltip'     => 'Apply some space around your images',
			'value'       => isset( $arr_settings['ssba_padding'] ) ? $arr_settings['ssba_padding'] : '',
		);

		// Font color.
		$opts9 = array(
			'form_group' => false,
			'type'       => 'colorpicker',
			'name'       => 'ssba_font_color',
			'label'      => 'Font Color',
			'tooltip'    => 'Choose the color of your share text',
			'value'      => isset( $arr_settings['ssba_font_color'] ) ? $arr_settings['ssba_font_color'] : '',
		);

		// Font color for plus.
		$opts9p = array(
			'form_group' => false,
			'type'       => 'colorpicker',
			'name'       => 'ssba_plus_font_color',
			'label'      => 'Font Color',
			'tooltip'    => 'Choose the color of your share text',
			'value'      => isset( $arr_settings['ssba_plus_font_color'] ) ? $arr_settings['ssba_plus_font_color'] : '',
		);

		// Font family.
		$opts10 = array(
			'form_group' => false,
			'type'       => 'select',
			'name'       => 'ssba_font_family',
			'label'      => 'Font Family',
			'tooltip'    => 'Choose a font available or inherit the font from your website',
			'selected'   => isset( $arr_settings['ssba_font_family'] ) ? $arr_settings['ssba_font_family'] : '',
			'options'    => array(
				'Reenie Beanie'           => 'Reenie Beanie',
				'Indie Flower'            => 'Indie Flower',
				'Inherit from my website' => '',
			),
		);

		// Font family for plus.
		$opts10p = array(
			'form_group' => false,
			'type'       => 'select',
			'name'       => 'ssba_plus_font_family',
			'label'      => 'Font Family',
			'tooltip'    => 'Choose a font available or inherit the font from your website',
			'selected'   => isset( $arr_settings['ssba_plus_font_family'] ) ? $arr_settings['ssba_plus_font_family'] : '',
			'options'    => array(
				'Reenie Beanie'           => 'Reenie Beanie',
				'Indie Flower'            => 'Indie Flower',
				'Inherit from my website' => '',
			),
		);

		// Enqueue the styles so preview can update.
		wp_enqueue_style( "{$this->plugin->assets_prefix}-reenie" );
		wp_enqueue_style( "{$this->plugin->assets_prefix}-indie" );

		// Font size.
		$opts11 = array(
			'form_group'  => false,
			'type'        => 'number_addon',
			'addon'       => 'px',
			'placeholder' => '20',
			'name'        => 'ssba_font_size',
			'label'       => 'Font Size',
			'tooltip'     => 'Set the size of the share text in pixels',
			'value'       => isset( $arr_settings['ssba_font_size'] ) ? $arr_settings['ssba_font_size'] : '',
		);

		// Font size for plus.
		$opts11p = array(
			'form_group'  => false,
			'type'        => 'number_addon',
			'addon'       => 'px',
			'placeholder' => '20',
			'name'        => 'ssba_plus_font_size',
			'label'       => 'Font Size',
			'tooltip'     => 'Set the size of the share text in pixels',
			'value'       => isset( $arr_settings['ssba_plus_font_size'] ) ? $arr_settings['ssba_plus_font_size'] : '',
		);

		// Font weight.
		$opts12 = array(
			'form_group' => false,
			'type'       => 'select',
			'name'       => 'ssba_font_weight',
			'label'      => 'Font Weight',
			'tooltip'    => 'Set the weight of the share text',
			'selected'   => isset( $arr_settings['ssba_font_weight'] ) ? $arr_settings['ssba_font_weight'] : '',
			'options'    => array(
				'Normal' => 'normal',
				'Bold'   => 'bold',
				'Light'  => 'light',
			),
		);

		// Font weight for plus.
		$opts12p = array(
			'form_group' => false,
			'type'       => 'select',
			'name'       => 'ssba_plus_font_weight',
			'label'      => 'Font Weight',
			'tooltip'    => 'Set the weight of the share text',
			'selected'   => isset( $arr_settings['ssba_plus_font_weight'] ) ? $arr_settings['ssba_plus_font_weight'] : '',
			'options'    => array(
				'Normal' => 'normal',
				'Bold'   => 'bold',
				'Light'  => 'light',
			),
		);

		// Text placement.
		$opts13 = array(
			'form_group' => false,
			'type'       => 'select',
			'name'       => 'ssba_text_placement',
			'label'      => 'Text placement',
			'tooltip'    => 'Choose where you want your text to be displayed, in relation to the buttons',
			'selected'   => isset( $arr_settings['ssba_text_placement'] ) ? $arr_settings['ssba_text_placement'] : '',
			'options'    => array(
				'Above' => 'above',
				'Left'  => 'left',
				'Right' => 'right',
				'Below' => 'below',
			),
		);

		// Text placement for plus.
		$opts13p = array(
			'form_group' => false,
			'type'       => 'select',
			'name'       => 'ssba_plus_text_placement',
			'label'      => 'Text placement',
			'tooltip'    => 'Choose where you want your text to be displayed, in relation to the buttons',
			'selected'   => isset( $arr_settings['ssba_plus_text_placement'] ) ? $arr_settings['ssba_plus_text_placement'] : '',
			'options'    => array(
				'Above' => 'above',
				'Left'  => 'left',
				'Right' => 'right',
				'Below' => 'below',
			),
		);

		// Container padding.
		$opts14 = array(
			'form_group'  => false,
			'type'        => 'number_addon',
			'addon'       => 'px',
			'placeholder' => '10',
			'name'        => 'ssba_div_padding',
			'label'       => 'Container Padding',
			'tooltip'     => 'Add some padding to your share container',
			'value'       => isset( $arr_settings['ssba_div_padding'] ) ? $arr_settings['ssba_div_padding'] : '',
		);

		// Div background color.
		$opts15 = array(
			'form_group' => false,
			'type'       => 'colorpicker',
			'name'       => 'ssba_div_background',
			'label'      => 'Container Background Color',
			'tooltip'    => 'Choose the color of your share container',
			'value'      => isset( $arr_settings['ssba_div_background'] ) ? $arr_settings['ssba_div_background'] : '',
		);

		// Div border color.
		$opts16 = array(
			'form_group' => false,
			'type'       => 'colorpicker',
			'name'       => 'ssba_div_border',
			'label'      => 'Container Border Color',
			'tooltip'    => 'Choose the color of your share container border',
			'value'      => isset( $arr_settings['ssba_div_border'] ) ? $arr_settings['ssba_div_border'] : '',
		);

		// Container border width.
		$opts17 = array(
			'form_group'  => false,
			'type'        => 'number_addon',
			'addon'       => 'px',
			'placeholder' => '1',
			'name'        => 'ssba_border_width',
			'label'       => 'Container Border Width',
			'tooltip'     => 'Set the width of the share container border',
			'value'       => isset( $arr_settings['ssba_border_width'] ) ? $arr_settings['ssba_border_width'] : '',
		);

		// Rounded container corners.
		$opts18 = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_div_rounded_corners',
			'label'      => 'Rounded Container Corners',
			'tooltip'    => 'Switch on to enable rounded corners for your share container',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_div_rounded_corners'] ) && 'Y' === $arr_settings['ssba_div_rounded_corners'] ? esc_attr( 'checked' ) : '',
		);

		// Share count.
		$opts19 = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_show_share_count',
			'label'      => 'Share Count',
			'tooltip'    => 'Check the box if you wish to enable share counts. Enabling this option will slow down the loading of any pages that use share buttons',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_show_share_count'] ) && 'Y' === $arr_settings['ssba_show_share_count'] ? esc_attr( 'checked' ) : null,
		);

		// Share count for plus.
		$opts19p = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_plus_show_share_count',
			'label'      => 'Share Count',
			'tooltip'    => 'Check the box if you wish to enable share counts. Enabling this option will slow down the loading of any pages that use share buttons',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_plus_show_share_count'] ) && 'Y' === $arr_settings['ssba_plus_show_share_count'] ? esc_attr( 'checked' ) : null,
		);

		// Share count for share bar.
		$opts19s = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_share_show_share_count',
			'label'      => 'Share Count',
			'tooltip'    => 'Check the box if you wish to enable share counts. Enabling this option will slow down the loading of any pages that use share buttons',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_share_show_share_count'] ) && 'Y' === $arr_settings['ssba_share_show_share_count'] ? esc_attr( 'checked' ) : null,
		);

		// Show count once.
		$opts20 = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_share_count_once',
			'label'      => 'Show Once',
			'tooltip'    => 'This option is recommended, it deactivates share counts for categories and archives allowing them to load more quickly',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_share_count_once'] ) && 'Y' === $arr_settings['ssba_share_count_once'] ? esc_attr( 'checked' ) : null,
		);

		// Show count once for plus.
		$opts20p = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_plus_share_count_once',
			'label'      => 'Show Once',
			'tooltip'    => 'This option is recommended, it deactivates share counts for categories and archives allowing them to load more quickly',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_plus_share_count_once'] ) && 'Y' === $arr_settings['ssba_plus_share_count_once'] ? esc_attr( 'checked' ) : null,
		);

		// Show count once for share bar.
		$opts20s = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_share_share_count_once',
			'label'      => 'Show Once',
			'tooltip'    => 'This option is recommended, it deactivates share counts for categories and archives allowing them to load more quickly',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_share_share_count_once'] ) && 'Y' === $arr_settings['ssba_share_share_count_once'] ? esc_attr( 'checked' ) : null,
		);

		// Share counters style.
		$opts21 = array(
			'form_group' => false,
			'type'       => 'select',
			'name'       => 'ssba_share_count_style',
			'label'      => 'Counters Style',
			'tooltip'    => 'Pick a setting to style the share counters',
			'selected'   => isset( $arr_settings['ssba_share_count_style'] ) ? $arr_settings['ssba_share_count_style'] : '',
			'options'    => array(
				'Default' => 'default',
				'White'   => 'white',
				'Blue'    => 'blue',
			),
		);

		// Newsharecounts.com enable.
		$opts22 = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'twitter_newsharecounts',
			'label'      => '',
			'tooltip'    => 'Switch on to enable the use of the newsharecounts.com API for Twitter share counts',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['twitter_newsharecounts'] ) && 'Y' === $arr_settings['twitter_newsharecounts'] ? esc_attr( 'checked' ) : null,
		);

		// Sharedcount enable.
		$opts23 = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'sharedcount_enabled',
			'label'      => 'Enable sharedcount.com API',
			'tooltip'    => 'Enable if you wish to enable the use of the sharedcount.com API',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['sharedcount_enabled'] ) && 'Y' === $arr_settings['sharedcount_enabled'] ? esc_attr( 'checked' ) : null,
		);

		// Sharedcount plan.
		$opts24 = array(
			'form_group' => false,
			'type'       => 'select',
			'name'       => 'sharedcount_plan',
			'label'      => 'sharedcount.com plan',
			'tooltip'    => 'Select your sharedcount.com plan',
			'selected'   => isset( $arr_settings['sharedcount_plan'] ) ? $arr_settings['sharedcount_plan'] : '',
			'options'    => array(
				'Free'     => 'free',
				'Plus'     => 'plus',
				'Business' => 'business',
			),
		);

		// Sharedcount api key.
		$opts25 = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => '9b17c12712c691491ef95f46c51ce3917118fdf9',
			'name'        => 'sharedcount_api_key',
			'label'       => 'sharedcount.com API Key',
			'tooltip'     => 'Add some text included in an email when people share that way',
			'value'       => isset( $arr_settings['sharedcount_api_key'] ) ? $arr_settings['sharedcount_api_key'] : '',
		);

		// Link to ssb.
		$opts26 = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_link_to_ssb',
			'label'      => 'Share Text Link',
			'tooltip'    => 'Enabling this will set your share text as a link to simplesharebuttons.com to help others learn of the plugin',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_link_to_ssb'] ) && 'Y' === $arr_settings['ssba_link_to_ssb'] ? esc_attr( 'checked' ) : null,
		);

		// Link to ssb for plus.
		$opts26p = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_plus_link_to_ssb',
			'label'      => 'Share Text Link',
			'tooltip'    => 'Enabling this will set your share text as a link to simplesharebuttons.com to help others learn of the plugin',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_plus_link_to_ssb'] ) && 'Y' === $arr_settings['ssba_plus_link_to_ssb'] ? esc_attr( 'checked' ) : null,
		);

		// Link to ssb for plus.
		$opts26s = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_share_link_to_ssb',
			'label'      => 'Share Text Link',
			'tooltip'    => 'Enabling this will set your share text as a link to simplesharebuttons.com to help others learn of the plugin',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_share_link_to_ssb'] ) && 'Y' === $arr_settings['ssba_share_link_to_ssb'] ? esc_attr( 'checked' ) : null,
		);

		// Content priority.
		$opts27 = array(
			'form_group'  => false,
			'type'        => 'number',
			'placeholder' => '10',
			'name'        => 'ssba_content_priority',
			'label'       => 'Content Priority',
			'tooltip'     => 'Set the priority for your share buttons within your content. 1-10, default is 10',
			'value'       => isset( $arr_settings['ssba_content_priority'] ) ? $arr_settings['ssba_content_priority'] : '',
		);

		// Share in new window.
		$opts28 = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_share_new_window',
			'label'      => 'Open links in a new window',
			'tooltip'    => 'Disabling this will make links open in the same window',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_share_new_window'] ) && 'Y' === $arr_settings['ssba_share_new_window'] ? esc_attr( 'checked' ) : null,
		);

		// Share in new window for plus.
		$opts28p = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_plus_share_new_window',
			'label'      => 'Open links in a new window',
			'tooltip'    => 'Disabling this will make links open in the same window',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_plus_share_new_window'] ) && 'Y' === $arr_settings['ssba_plus_share_new_window'] ? esc_attr( 'checked' ) : null,
		);

		// Share in new window for share bar.
		$opts28s = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_share_share_new_window',
			'label'      => 'Open links in a new window',
			'tooltip'    => 'Disabling this will make links open in the same window',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_share_share_new_window'] ) && 'Y' === $arr_settings['ssba_share_share_new_window'] ? esc_attr( 'checked' ) : null,
		);

		// Nofollow.
		$opts29 = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_rel_nofollow',
			'label'      => 'Add rel="nofollow"',
			'tooltip'    => 'Enable this to add nofollow to all share links',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_rel_nofollow'] ) && 'Y' === $arr_settings['ssba_rel_nofollow'] ? esc_attr( 'checked' ) : null,
		);

		// Nofollow for plus.
		$opts29p = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_plus_rel_nofollow',
			'label'      => 'Add rel="nofollow"',
			'tooltip'    => 'Enable this to add nofollow to all share links',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_plus_rel_nofollow'] ) && 'Y' === $arr_settings['ssba_plus_rel_nofollow'] ? esc_attr( 'checked' ) : null,
		);

		// Nofollow for share bar.
		$opts29s = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_share_rel_nofollow',
			'label'      => 'Add rel="nofollow"',
			'tooltip'    => 'Enable this to add nofollow to all share links',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_share_rel_nofollow'] ) && 'Y' === $arr_settings['ssba_share_rel_nofollow'] ? esc_attr( 'checked' ) : null,
		);

		// Widget share text.
		$opts30 = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'Keeping sharing simple...',
			'name'        => 'ssba_widget_text',
			'label'       => 'Widget Share Text',
			'tooltip'     => 'Add custom share text when used as a widget',
			'value'       => isset( $arr_settings['ssba_widget_text'] ) ? $arr_settings['ssba_widget_text'] : '',
		);

		// Widget share text for plus.
		$opts30p = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'Keeping sharing simple...',
			'name'        => 'ssba_plus_widget_text',
			'label'       => 'Widget Share Text',
			'tooltip'     => 'Add custom share text when used as a widget',
			'value'       => isset( $arr_settings['ssba_plus_widget_text'] ) ? $arr_settings['ssba_plus_widget_text'] : '',
		);

		// Widget share text for share bar.
		$opts30s = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'Keeping sharing simple...',
			'name'        => 'ssba_share_widget_text',
			'label'       => 'Widget Share Text',
			'tooltip'     => 'Add custom share text when used as a widget',
			'value'       => isset( $arr_settings['ssba_share_widget_text'] ) ? $arr_settings['ssba_share_widget_text'] : '',
		);

		// Email share text.
		$opts31 = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'Share by email...',
			'name'        => 'ssba_email_message',
			'label'       => 'Email Text',
			'tooltip'     => 'Add some text included in an email when people share that way',
			'value'       => isset( $arr_settings['ssba_email_message'] ) ? $arr_settings['ssba_email_message'] : '',
		);

		// Email share text for plus.
		$opts31p = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'Share by email...',
			'name'        => 'ssba_plus_email_message',
			'label'       => 'Email Text',
			'tooltip'     => 'Add some text included in an email when people share that way',
			'value'       => isset( $arr_settings['ssba_plus_email_message'] ) ? $arr_settings['ssba_plus_email_message'] : '',
		);

		// Email share text for share bar.
		$opts31s = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'Share by email...',
			'name'        => 'ssba_share_email_message',
			'label'       => 'Email Text',
			'tooltip'     => 'Add some text included in an email when people share that way',
			'value'       => isset( $arr_settings['ssba_share_email_message'] ) ? $arr_settings['ssba_share_email_message'] : '',
		);

		// Facebook app id.
		$opts32 = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => '123456789123',
			'name'        => 'facebook_app_id',
			'label'       => 'Facebook App ID',
			'tooltip'     => 'Enter your Facebook App ID, e.g. 123456789123',
			'value'       => isset( $arr_settings['facebook_app_id'] ) ? $arr_settings['facebook_app_id'] : '',
			'disabled'    => 'Y' !== $arr_settings['accepted_sharethis_terms'] ? esc_attr( 'disabled' ) : null,
		);

		// Facebook app id for plus.
		$opts32p = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => '123456789123',
			'name'        => 'plus_facebook_app_id',
			'label'       => 'Facebook App ID',
			'tooltip'     => 'Enter your Facebook App ID, e.g. 123456789123',
			'value'       => isset( $arr_settings['plus_facebook_app_id'] ) ? $arr_settings['plus_facebook_app_id'] : '',
			'disabled'    => 'Y' !== $arr_settings['accepted_sharethis_terms'] ? esc_attr( 'disabled' ) : null,
		);

		// Facebook app id for share bar.
		$opts32s = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => '123456789123',
			'name'        => 'share_facebook_app_id',
			'label'       => 'Facebook App ID',
			'tooltip'     => 'Enter your Facebook App ID, e.g. 123456789123',
			'value'       => isset( $arr_settings['share_facebook_app_id'] ) ? $arr_settings['share_facebook_app_id'] : '',
			'disabled'    => 'Y' !== $arr_settings['accepted_sharethis_terms'] ? esc_attr( 'disabled' ) : null,
		);

		// Facebook insights.
		$opts33 = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'facebook_insights',
			'label'      => 'Facebook Insights',
			'tooltip'    => 'Enable this feature to enable Facebook Insights',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['facebook_insights'] ) && 'Y' === $arr_settings['facebook_insights'] ? 'checked' : null,
			'disabled'   => 'Y' !== $arr_settings['accepted_sharethis_terms'] ? 'disabled' : null,
		);

		// Facebook insights for plus.
		$opts33p = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'plus_facebook_insights',
			'label'      => 'Facebook Insights',
			'tooltip'    => 'Enable this feature to enable Facebook Insights',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['plus_facebook_insights'] ) && 'Y' === $arr_settings['plus_facebook_insights'] ? 'checked' : null,
			'disabled'   => 'Y' !== $arr_settings['accepted_sharethis_terms'] ? 'disabled' : null,
		);

		// Facebook insights for share bar.
		$opts33s = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'share_facebook_insights',
			'label'      => 'Facebook Insights',
			'tooltip'    => 'Enable this feature to enable Facebook Insights',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['share_facebook_insights'] ) && 'Y' === $arr_settings['share_facebook_insights'] ? 'checked' : null,
			'disabled'   => 'Y' !== $arr_settings['accepted_sharethis_terms'] ? 'disabled' : null,
		);

		// Twitter share text.
		$opts34 = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'Shared by Twitter...',
			'name'        => 'ssba_twitter_text',
			'label'       => 'Twitter Text',
			'tooltip'     => 'Add some custom text for when people share via Twitter',
			'value'       => isset( $arr_settings['ssba_twitter_text'] ) ? $arr_settings['ssba_twitter_text'] : '',
		);

		// Twitter share text for plus.
		$opts34p = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'Shared by Twitter...',
			'name'        => 'ssba_plus_twitter_text',
			'label'       => 'Twitter Text',
			'tooltip'     => 'Add some custom text for when people share via Twitter',
			'value'       => isset( $arr_settings['ssba_plus_twitter_text'] ) ? $arr_settings['ssba_plus_twitter_text'] : '',
		);

		// Twitter share text for share bar.
		$opts34s = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'Shared by Twitter...',
			'name'        => 'ssba_share_twitter_text',
			'label'       => 'Twitter Text',
			'tooltip'     => 'Add some custom text for when people share via Twitter',
			'value'       => isset( $arr_settings['ssba_share_twitter_text'] ) ? $arr_settings['ssba_share_twitter_text'] : '',
		);

		// Flattr user id.
		$opts35 = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'davidsneal',
			'name'        => 'ssba_flattr_user_id',
			'label'       => 'Flattr User ID',
			'tooltip'     => 'Enter your Flattr ID, e.g. davidsneal',
			'value'       => isset( $arr_settings['ssba_flattr_user_id'] ) ? $arr_settings['ssba_flattr_user_id'] : '',
		);

		// Flattr user id for plus.
		$opts35p = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'davidsneal',
			'name'        => 'ssba_plus_flattr_user_id',
			'label'       => 'Flattr User ID',
			'tooltip'     => 'Enter your Flattr ID, e.g. davidsneal',
			'value'       => isset( $arr_settings['ssba_plus_flattr_user_id'] ) ? $arr_settings['ssba_plus_flattr_user_id'] : '',
		);

		// Flattr user id for share bar.
		$opts35s = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'davidsneal',
			'name'        => 'ssba_share_flattr_user_id',
			'label'       => 'Flattr User ID',
			'tooltip'     => 'Enter your Flattr ID, e.g. davidsneal',
			'value'       => isset( $arr_settings['ssba_share_flattr_user_id'] ) ? $arr_settings['ssba_share_flattr_user_id'] : '',
		);

		// Flattr url.
		$opts36 = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'https://simplesharebuttons.com',
			'name'        => 'ssba_flattr_url',
			'label'       => 'Flattr URL',
			'tooltip'     => 'This option is perfect for dedicated sites, e.g. https://simplesharebuttons.com',
			'value'       => isset( $arr_settings['ssba_flattr_url'] ) ? $arr_settings['ssba_flattr_url'] : '',
		);

		// Flattr url for plus.
		$opts36p = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'https://simplesharebuttons.com',
			'name'        => 'ssba_plus_flattr_url',
			'label'       => 'Flattr URL',
			'tooltip'     => 'This option is perfect for dedicated sites, e.g. https://simplesharebuttons.com',
			'value'       => isset( $arr_settings['ssba_plus_flattr_url'] ) ? $arr_settings['ssba_plus_flattr_url'] : '',
		);

		// Flattr url for share bar.
		$opts36s = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'https://simplesharebuttons.com',
			'name'        => 'ssba_share_flattr_url',
			'label'       => 'Flattr URL',
			'tooltip'     => 'This option is perfect for dedicated sites, e.g. https://simplesharebuttons.com',
			'value'       => isset( $arr_settings['ssba_share_flattr_url'] ) ? $arr_settings['ssba_share_flattr_url'] : '',
		);

		// Buffer text.
		$opts37 = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'Shared by Buffer...',
			'name'        => 'ssba_buffer_text',
			'label'       => 'Custom Buffer Text',
			'tooltip'     => 'Add some custom text for when people share via Buffer',
			'value'       => isset( $arr_settings['ssba_buffer_text'] ) ? $arr_settings['ssba_buffer_text'] : '',
		);

		// Buffer text for plus.
		$opts37p = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'Shared by Buffer...',
			'name'        => 'ssba_plus_buffer_text',
			'label'       => 'Custom Buffer Text',
			'tooltip'     => 'Add some custom text for when people share via Buffer',
			'value'       => isset( $arr_settings['ssba_plus_buffer_text'] ) ? $arr_settings['ssba_plus_buffer_text'] : '',
		);

		// Buffer text for share bar.
		$opts37s = array(
			'form_group'  => false,
			'type'        => 'text',
			'placeholder' => 'Shared by Buffer...',
			'name'        => 'ssba_share_buffer_text',
			'label'       => 'Custom Buffer Text',
			'tooltip'     => 'Add some custom text for when people share via Buffer',
			'value'       => isset( $arr_settings['ssba_share_buffer_text'] ) ? $arr_settings['ssba_share_buffer_text'] : '',
		);

		// Pin featured images.
		$opts38 = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_pinterest_featured',
			'label'      => 'Pin Featured Images',
			'tooltip'    => 'Force the use of featured images for posts/pages when pinning',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_pinterest_featured'] ) && 'Y' === $arr_settings['ssba_pinterest_featured'] ? 'checked' : null,
		);

		// Pin featured images for plus.
		$opts38p = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_plus_pinterest_featured',
			'label'      => 'Pin Featured Images',
			'tooltip'    => 'Force the use of featured images for posts/pages when pinning',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_plus_pinterest_featured'] ) && 'Y' === $arr_settings['ssba_plus_pinterest_featured'] ? 'checked' : null,
		);

		// Pin featured images for share bar.
		$opts38s = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_share_pinterest_featured',
			'label'      => 'Pin Featured Images',
			'tooltip'    => 'Force the use of featured images for posts/pages when pinning',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_share_pinterest_featured'] ) && 'Y' === $arr_settings['ssba_share_pinterest_featured'] ? 'checked' : null,
		);

		// Default pinterest image.
		$opts39 = array(
			'form_group' => false,
			'type'       => 'image_upload',
			'name'       => 'ssba_default_pinterest',
			'label'      => 'Default Pinterest Image',
			'tooltip'    => 'Upload a default Pinterest image',
			'value'      => isset( $arr_settings['ssba_default_pinterest'] ) ? $arr_settings['ssba_default_pinterest'] : '',
		);

		// Default pinterest image for plus.
		$opts39p = array(
			'form_group' => false,
			'type'       => 'image_upload',
			'name'       => 'ssba_plus_default_pinterest',
			'label'      => 'Default Pinterest Image',
			'tooltip'    => 'Upload a default Pinterest image',
			'value'      => isset( $arr_settings['ssba_plus_default_pinterest'] ) ? $arr_settings['ssba_plus_default_pinterest'] : '',
		);

		// Default pinterest image for share bar.
		$opts39s = array(
			'form_group' => false,
			'type'       => 'image_upload',
			'name'       => 'ssba_share_default_pinterest',
			'label'      => 'Default Pinterest Image',
			'tooltip'    => 'Upload a default Pinterest image',
			'value'      => isset( $arr_settings['ssba_share_default_pinterest'] ) ? $arr_settings['ssba_share_default_pinterest'] : '',
		);

		// Additional css.
		$opts40 = array(
			'form_group' => false,
			'type'       => 'textarea',
			'rows'       => '15',
			'class'      => 'code-font',
			'name'       => 'ssba_additional_css',
			'label'      => 'Additional CSS',
			'tooltip'    => 'Add your own additional CSS if you wish',
			'value'      => isset( $arr_settings['ssba_additional_css'] ) ? $arr_settings['ssba_additional_css'] : '',
		);

		// Additional css for plus.
		$opts40p = array(
			'form_group' => false,
			'type'       => 'textarea',
			'rows'       => '15',
			'class'      => 'code-font',
			'name'       => 'ssba_plus_additional_css',
			'label'      => 'Additional CSS',
			'tooltip'    => 'Add your own additional CSS if you wish',
			'value'      => isset( $arr_settings['ssba_plus_additional_css'] ) ? $arr_settings['ssba_plus_additional_css'] : '',
		);

		// Additional css for share.
		$opts40s = array(
			'form_group' => false,
			'type'       => 'textarea',
			'rows'       => '15',
			'class'      => 'code-font',
			'name'       => 'ssba_share_additional_css',
			'label'      => 'Additional CSS',
			'tooltip'    => 'Add your own additional CSS if you wish',
			'value'      => isset( $arr_settings['ssba_share_additional_css'] ) ? $arr_settings['ssba_share_additional_css'] : '',
		);

		// Enable custom css.
		$opts41 = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_custom_styles_enabled',
			'label'      => 'Enable Custom CSS',
			'tooltip'    => 'Switch on to disable all SSBA styles and use your own custom CSS',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_custom_styles_enabled'] ) && 'Y' === $arr_settings['ssba_custom_styles_enabled'] ? 'checked' : null,
		);

		// Enable custom css for plus.
		$opts41p = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_plus_custom_styles_enabled',
			'label'      => 'Enable Custom CSS',
			'tooltip'    => 'Switch on to disable all SSBA styles and use your own custom CSS',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_plus_custom_styles_enabled'] ) && 'Y' === $arr_settings['ssba_plus_custom_styles_enabled'] ? 'checked' : null,
		);

		// Enable custom css for share bar.
		$opts41s = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_share_custom_styles_enabled',
			'label'      => 'Enable Custom CSS',
			'tooltip'    => 'Switch on to disable all SSBA styles and use your own custom CSS',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_share_custom_styles_enabled'] ) && 'Y' === $arr_settings['ssba_share_custom_styles_enabled'] ? 'checked' : null,
		);

		// Custom css.
		$opts42 = array(
			'form_group' => false,
			'type'       => 'textarea',
			'rows'       => '15',
			'class'      => 'code-font',
			'name'       => 'ssba_custom_styles',
			'label'      => 'Custom CSS',
			'tooltip'    => 'Enter in your own custom CSS for your share buttons',
			'value'      => isset( $arr_settings['ssba_custom_styles'] ) ? $arr_settings['ssba_custom_styles'] : '',
		);

		// Custom css for plus.
		$opts42p = array(
			'form_group' => false,
			'type'       => 'textarea',
			'rows'       => '15',
			'class'      => 'code-font',
			'name'       => 'ssba_plus_custom_styles',
			'label'      => 'Custom CSS',
			'tooltip'    => 'Enter in your own custom CSS for your share buttons',
			'value'      => isset( $arr_settings['ssba_plus_custom_styles'] ) ? $arr_settings['ssba_plus_custom_styles'] : '',
		);

		// Custom css for share bar.
		$opts42s = array(
			'form_group' => false,
			'type'       => 'textarea',
			'rows'       => '15',
			'class'      => 'code-font',
			'name'       => 'ssba_share_custom_styles',
			'label'      => 'Custom CSS',
			'tooltip'    => 'Enter in your own custom CSS for your share buttons',
			'value'      => isset( $arr_settings['ssba_share_custom_styles'] ) ? $arr_settings['ssba_share_custom_styles'] : '',
		);

		// Switch to new buttons.
		$opts43 = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_new_buttons',
			'label'      => 'Plus Share Buttons',
			'tooltip'    => 'If "On" new buttons replace the old on your site.',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_new_buttons'] ) && 'Y' === $arr_settings['ssba_new_buttons'] ? 'checked' : null,
		);

		// Select style of new buttons.
		$opts44 = array(
			'form_group' => false,
			'type'       => 'select',
			'name'       => 'ssba_share_button_style',
			'label'      => 'Theme',
			'tooltip'    => 'Choose the style of the new buttons',
			'selected'   => isset( $arr_settings['ssba_share_button_style'] ) ? $arr_settings['ssba_share_button_style'] : '',
			'options'    => array(
				'Round'             => 1,
				'Square'            => 2,
				'Logo Name'         => 3,
				'Rounded'           => 4,
				'3D'                => 5,
				'Border Round'      => 6,
				'Border Logo Name'  => 7,
				'Black Border'      => 8,
				'Underline'         => 9,
				'Auto Square'       => 10,
				'Name'              => 11,
			),
		);

		// Locations.
		$opts48 = array(
			'form_group' => false,
			'label'      => 'Locations',
			'tooltip'    => 'Enable the locations you wish for plus buttons to appear',
			'value'      => 'Y',
			'checkboxes' => $locs3,
		);

		// Placement.
		$opts49 = array(
			'form_group' => false,
			'type'       => 'select',
			'name'       => 'ssba_before_or_after_plus',
			'label'      => 'Placement',
			'tooltip'    => 'Place share buttons before or after your content',
			'selected'   => isset( $arr_settings['ssba_before_or_after_plus'] ) ? $arr_settings['ssba_before_or_after_plus'] : '',
			'options'    => array(
				'After'  => 'after',
				'Before' => 'before',
				'Both'   => 'both',
			),
		);

		// Locations.
		$opts45 = array(
			'form_group' => false,
			'label'      => 'Locations',
			'tooltip'    => 'Enable the locations you wish for share buttons to appear',
			'value'      => 'Y',
			'checkboxes' => $locs2,
		);

		// Select style of share bar.
		$opts46 = array(
			'form_group' => false,
			'type'       => 'select',
			'name'       => 'ssba_share_bar_style',
			'label'      => 'Style',
			'tooltip'    => 'Choose the style of the share bar buttons',
			'selected'   => isset( $arr_settings['ssba_share_bar_style'] ) ? $arr_settings['ssba_share_bar_style'] : '',
			'options'    => array(
				'Round'             => 1,
				'Square'            => 2,
				'Logo Name'         => 3,
				'Rounded'           => 4,
				'3D'                => 5,
				'Border Round'      => 6,
				'Border Logo Name'  => 7,
				'Black Border'      => 8,
				'Underline'         => 9,
				'Name'              => 11,
			),
		);

		// Select position of share bar.
		$opts47 = array(
			'form_group' => false,
			'type'       => 'select',
			'name'       => 'ssba_share_bar_position',
			'label'      => 'Alignment',
			'tooltip'    => 'Choose the share bar position',
			'selected'   => isset( $arr_settings['ssba_share_bar_position'] ) ? $arr_settings['ssba_share_bar_position'] : '',
			'options'    => array(
				'Sticky Left'   => 'left',
				'Sticky Right'  => 'right',
			),
		);

		// Plus buttons height.
		$plus_height = array(
			'form_group'  => false,
			'type'        => 'number_addon',
			'addon'       => 'px',
			'placeholder' => '48',
			'name'        => 'ssba_plus_height',
			'label'       => 'Height',
			'tooltip'     => 'Set the height of the plus buttons',
			'value'       => isset( $arr_settings['ssba_plus_height'] ) ? $arr_settings['ssba_plus_height'] : '',
		);

		// Plus buttons width.
		$plus_width = array(
			'form_group'  => false,
			'type'        => 'number_addon',
			'addon'       => 'px',
			'placeholder' => '48',
			'name'        => 'ssba_plus_width',
			'label'       => 'Width',
			'tooltip'     => 'Set the width of the plus buttons',
			'value'       => isset( $arr_settings['ssba_plus_width'] ) ? $arr_settings['ssba_plus_width'] : '',
		);

		// Plus icon size.
		$plus_icon_size = array(
			'form_group'  => false,
			'type'        => 'number_addon',
			'addon'       => 'px',
			'placeholder' => '24',
			'name'        => 'ssba_plus_icon_size',
			'label'       => 'Icon Size',
			'tooltip'     => 'Set the icon size of the plus buttons',
			'value'       => isset( $arr_settings['ssba_plus_icon_size'] ) ? $arr_settings['ssba_plus_icon_size'] : '',
		);

		// Plus button margin.
		$plus_margin = array(
			'form_group'  => false,
			'type'        => 'number_addon',
			'addon'       => 'px',
			'placeholder' => '12',
			'name'        => 'ssba_plus_margin',
			'label'       => 'Margin',
			'tooltip'     => 'Set the margin of the plus buttons',
			'value'       => isset( $arr_settings['ssba_plus_margin'] ) ? $arr_settings['ssba_plus_margin'] : '',
		);

		// Plus button color override.
		$plus_button_color = array(
			'form_group' => false,
			'type'       => 'colorpicker',
			'name'       => 'ssba_plus_button_color',
			'label'      => 'Button Color',
			'tooltip'    => 'Choose the color for all plus buttons',
			'value'      => isset( $arr_settings['ssba_plus_button_color'] ) ? $arr_settings['ssba_plus_button_color'] : '',
		);

		// Plus button hover color override.
		$plus_hover_color = array(
			'form_group' => false,
			'type'       => 'colorpicker',
			'name'       => 'ssba_plus_button_hover_color',
			'label'      => 'Hover Color',
			'tooltip'    => 'Choose the color for all plus buttons hover',
			'value'      => isset( $arr_settings['ssba_plus_button_hover_color'] ) ? $arr_settings['ssba_plus_button_hover_color'] : '',
		);

		// Plus icon color override.
		$plus_icon_color = array(
			'form_group' => false,
			'type'       => 'colorpicker',
			'name'       => 'ssba_plus_icon_color',
			'label'      => 'Icon Color',
			'tooltip'    => 'Choose the color for all plus button icons',
			'value'      => isset( $arr_settings['ssba_plus_icon_color'] ) ? $arr_settings['ssba_plus_icon_color'] : '',
		);

		// Plus button color override.
		$plus_icon_hover_color = array(
			'form_group' => false,
			'type'       => 'colorpicker',
			'name'       => 'ssba_plus_icon_hover_color',
			'label'      => 'Icon Hover Color',
			'tooltip'    => 'Choose the color for all plus button icons hover',
			'value'      => isset( $arr_settings['ssba_plus_icon_hover_color'] ) ? $arr_settings['ssba_plus_icon_hover_color'] : '',
		);

		// share buttons height.
		$share_height = array(
			'form_group'  => false,
			'type'        => 'number_addon',
			'addon'       => 'px',
			'placeholder' => '48',
			'name'        => 'ssba_share_height',
			'label'       => 'Height',
			'tooltip'     => 'Set the height of the share bar buttons',
			'value'       => isset( $arr_settings['ssba_share_height'] ) ? $arr_settings['ssba_share_height'] : '',
		);

		// share buttons width.
		$share_width = array(
			'form_group'  => false,
			'type'        => 'number_addon',
			'addon'       => 'px',
			'placeholder' => '48',
			'name'        => 'ssba_share_width',
			'label'       => 'Width',
			'tooltip'     => 'Set the width of the share bar buttons',
			'value'       => isset( $arr_settings['ssba_share_width'] ) ? $arr_settings['ssba_share_width'] : '',
		);

		// share icon size.
		$share_icon_size = array(
			'form_group'  => false,
			'type'        => 'number_addon',
			'addon'       => 'px',
			'placeholder' => '24',
			'name'        => 'ssba_share_icon_size',
			'label'       => 'Icon Size',
			'tooltip'     => 'Set the icon size of the share bar buttons',
			'value'       => isset( $arr_settings['ssba_share_icon_size'] ) ? $arr_settings['ssba_share_icon_size'] : '',
		);

		// share button margin.
		$share_margin = array(
			'form_group'  => false,
			'type'        => 'number_addon',
			'addon'       => 'px',
			'placeholder' => '12',
			'name'        => 'ssba_share_margin',
			'label'       => 'Margin',
			'tooltip'     => 'Set the margin of the share bar buttons',
			'value'       => isset( $arr_settings['ssba_share_margin'] ) ? $arr_settings['ssba_share_margin'] : '',
		);

		// share button color override.
		$share_button_color = array(
			'form_group' => false,
			'type'       => 'colorpicker',
			'name'       => 'ssba_share_button_color',
			'label'      => 'Button Color',
			'tooltip'    => 'Choose the color for all share bar buttons',
			'value'      => isset( $arr_settings['ssba_share_button_color'] ) ? $arr_settings['ssba_share_button_color'] : '',
		);

		// share button hover color override.
		$share_hover_color = array(
			'form_group' => false,
			'type'       => 'colorpicker',
			'name'       => 'ssba_share_button_hover_color',
			'label'      => 'Hover Color',
			'tooltip'    => 'Choose the color for all share bar buttons hover',
			'value'      => isset( $arr_settings['ssba_share_button_hover_color'] ) ? $arr_settings['ssba_share_button_hover_color'] : '',
		);

		// share icon color override.
		$share_icon_color = array(
			'form_group' => false,
			'type'       => 'colorpicker',
			'name'       => 'ssba_share_icon_color',
			'label'      => 'Icon Color',
			'tooltip'    => 'Choose the color for all share bar button icons',
			'value'      => isset( $arr_settings['ssba_share_icon_color'] ) ? $arr_settings['ssba_share_icon_color'] : '',
		);

		// share button color override.
		$share_icon_hover_color = array(
			'form_group' => false,
			'type'       => 'colorpicker',
			'name'       => 'ssba_share_icon_hover_color',
			'label'      => 'Icon Hover Color',
			'tooltip'    => 'Choose the color for all share bar button icons hover',
			'value'      => isset( $arr_settings['ssba_share_icon_hover_color'] ) ? $arr_settings['ssba_share_icon_hover_color'] : '',
		);

		// Enable share bar.
		$share_bar = array(
			'form_group' => false,
			'type'       => 'checkbox',
			'name'       => 'ssba_share_bar',
			'label'      => 'Share Bar',
			'tooltip'    => 'If "On" share bar will appear on your site.',
			'value'      => 'Y',
			'checked'    => isset( $arr_settings['ssba_share_bar'] ) && 'Y' === $arr_settings['ssba_share_bar'] ? 'checked' : null,
		);

		// Share bar display.
		$share_bar_display = array(
			'form_group' => false,
			'label'      => 'Display on',
			'tooltip'    => 'Disable to hide on desktop or mobile views',
			'value'      => 'Y',
			'checkboxes' => $display_loc,
		);

		// share button mobile breakpoint.
		$mobile_breakpoint = array(
			'form_group'  => false,
			'type'        => 'number_addon',
			'addon'       => 'px',
			'placeholder' => '780',
			'name'        => 'ssba_mobile_breakpoint',
			'label'       => 'Mobile Breakpoint',
			'tooltip'     => 'Set the share bar mobile breakpoint when it centers on screen',
			'value'       => isset( $arr_settings['ssba_mobile_breakpoint'] ) ? $arr_settings['ssba_mobile_breakpoint'] : '',
		);

		// Notices.
		$notice = get_option( 'ssba_dismiss_notice' );

		// All buttons.
		$arr_buttons = array_values( json_decode( get_option( 'ssba_buttons' ), true ) );
		$selected_buttons = explode( ',', $arr_settings['ssba_selected_buttons'] );

		// Custom button key.
		$custom_buttons = $this->get_custom_button_key( $arr_settings );

		foreach ( $arr_buttons as $button_name ) {
			$new_name = str_replace( '+', '', str_replace( ' ', '_', strtolower( $button_name['full_name'] ) ) );
			$new_arr_buttons[ $new_name ] = $button_name;
		}

		foreach ( $selected_buttons as $button ) {
			if ( isset( $new_arr_buttons[ $button ] ) ) {
				$selected_button_array[] = $new_arr_buttons[ $button ];
			}
		}

		foreach ( $arr_buttons as $non_buttons ) {
			if ( ! in_array( $non_buttons, $selected_button_array, true ) ) {
				$non_selected_buttons[] = $non_buttons;
			}
		}

		$arr_buttons = array_merge( $selected_button_array, $non_selected_buttons );

		include_once( "{$this->plugin->dir_path}/templates/admin-panel.php" );
	}

	/**
	 * Get an html formatted of currently selected and ordered buttons.
	 *
	 * @param array $str_selected_ssba The selected buttons.
	 * @param array $arr_settings The current ssba settings.
	 *
	 * @return string
	 */
	public function get_selected_ssba( $str_selected_ssba, $arr_settings ) {
		// Variables.
		$html_selected_list = '';

		// Prepare array of buttons.
		$arr_buttons = json_decode( get_option( 'ssba_buttons' ), true );

		// If there are some selected buttons.
		if ( '' !== $str_selected_ssba && null !== $str_selected_ssba && false !== $str_selected_ssba ) {
			// Explode saved include list and add to a new array.
			$arr_selected_ssba = explode( ',', $str_selected_ssba );

			// Check if array is not empty.
			if ( '' !== $arr_selected_ssba ) {
				// For each included button.
				foreach ( $arr_selected_ssba as $str_selected ) {
					// If share this terms haven't been accepted and it's the facebook save button then make the button look disabled.
					$disabled = 'Y' !== $arr_settings['accepted_sharethis_terms'] && 'facebook_save' === $str_selected ? 'style="background-color:#eaeaea;"' : null;

					// Add a list item for each selected option.
					$html_selected_list .= '<li class="ssbp-option-item" id="' . esc_attr( $str_selected ) . '"><a title="' . esc_attr( $arr_buttons[ $str_selected ]['full_name'] ) . '" href="javascript:;" class="ssbp-btn ssbp-' . esc_attr( $str_selected ) . '" ' . esc_attr( $disabled ) . '></a></li>';
				}
			}
		}

		// Return html list options.
		return $html_selected_list;
	}

	/**
	 * Custom button key.
	 *
	 * @param array $arr_settings The current site settings.
	 */
	public function get_custom_button_key( $arr_settings ) {
		$custom_array = array(
			'facebook'    => isset( $arr_settings['ssba_custom_facebook'] ) ? $arr_settings['ssba_custom_facebook'] : '',
			'google'      => isset( $arr_settings['ssba_custom_google'] ) ? $arr_settings['ssba_custom_google'] : '',
			'twitter'     => isset( $arr_settings['ssba_custom_twitter'] ) ? $arr_settings['ssba_custom_twitter'] : '',
			'linkedin'    => isset( $arr_settings['ssba_custom_linkedin'] ) ? $arr_settings['ssba_custom_linkedin'] : '',
			'flattr'      => isset( $arr_settings['ssba_custom_flattr'] ) ? $arr_settings['ssba_custom_flattr'] : '',
			'pinterest'   => isset( $arr_settings['ssba_custom_pinterest'] ) ? $arr_settings['ssba_custom_pinterest'] : '',
			'print'       => isset( $arr_settings['ssba_custom_print'] ) ? $arr_settings['ssba_custom_print'] : '',
			'reddit'      => isset( $arr_settings['ssba_custom_reddit'] ) ? $arr_settings['ssba_custom_reddit'] : '',
			'stumbleupon' => isset( $arr_settings['ssba_custom_stumbleupon'] ) ? $arr_settings['ssba_custom_stumbleupon'] : '',
			'tumblr'      => isset( $arr_settings['ssba_custom_tumblr'] ) ? $arr_settings['ssba_custom_tumblr'] : '',
			'vk'          => isset( $arr_settings['ssba_custom_vk'] ) ? $arr_settings['ssba_custom_vk'] : '',
			'whatsapp'    => isset( $arr_settings['ssba_custom_whatsapp'] ) ? $arr_settings['ssba_custom_whatsapp'] : '',
			'xing'        => isset( $arr_settings['ssba_custom_xing'] ) ? $arr_settings['ssba_custom_xing'] : '',
			'yummly'      => isset( $arr_settings['ssba_custom_yummly'] ) ? $arr_settings['ssba_custom_yummly'] : '',
		);

		return $custom_array;
	}

	/**
	 * Get available share buttons.
	 *
	 * @param array $str_selected_ssba The selected buttons.
	 * @param array $arr_settings The current ssba settings.
	 *
	 * @return string
	 */
	public function get_available_ssba( $str_selected_ssba, $arr_settings ) {
		// Variables.
		$html_available_list = '';

		// Prepare array of buttons.
		$arr_buttons = json_decode( get_option( 'ssba_buttons' ), true );

		// Explode saved include list and add to a new array.
		$arr_selected_ssba = explode( ',', $str_selected_ssba );

		// Extract the available buttons.
		$arr_available_ssba = array_diff( array_keys( $arr_buttons ), $arr_selected_ssba );

		// Check if array is not empty.
		if ( '' !== $arr_selected_ssba ) {
			// For each included button.
			foreach ( $arr_available_ssba as $str_available ) {
				// If share this terms haven't been accepted and it's the facebook save button then make the button look disabled.
				$disabled = 'Y' !== $arr_settings['accepted_sharethis_terms'] && 'facebook_save' === $str_available ? 'style="background-color:#eaeaea;"' : null;

				// Add a list item for each available option.
				$html_available_list .= '<li class="ssbp-option-item" id="' . esc_attr( $str_available ) . '"><a title="' . esc_attr( $arr_buttons[ $str_available ]['full_name'] ) . '" href="javascript:;" class="ssbp-btn ssbp-' . esc_attr( $str_available ) . '" ' . esc_attr( $disabled ) . '></a></li>';
			}
		}

		// Return html list options.
		return $html_available_list;
	}

	/**
	 * Get ssbp font family.
	 *
	 * @return string
	 */
	public function get_font_family() {
		return "@font-face {
				font-family: 'ssbp';
				src:url('{$this->plugin->dir_url}fonts/ssbp.eot?xj3ol1');
				src:url('{$this->plugin->dir_url}fonts/ssbp.eot?#iefixxj3ol1') format('embedded-opentype'),
					url('{$this->plugin->dir_url}fonts/ssbp.woff?xj3ol1') format('woff'),
					url('{$this->plugin->dir_url}fonts/ssbp.ttf?xj3ol1') format('truetype'),
					url('{$this->plugin->dir_url}fonts/ssbp.svg?xj3ol1#ssbp') format('svg');
				font-weight: normal;
				font-style: normal;

				/* Better Font Rendering =========== */
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
			}";
	}

	/**
	 * Add additional admin styles
	 *
	 * @action admin_enqueue_scripts
	 */
	public function additional_styles() {
		$html_share_buttons_form = '';

		// Get settings.
		$arr_settings = $this->class_ssba->get_ssba_settings();

		// If user is accepting terms.
		if ( isset( $_GET['accept-terms'] ) && 'Y' === $_GET['accept-terms'] ) { // WPCS: CSRF ok.
			// Save acceptance.
			$this->class_ssba->ssba_update_options( array(
				'accepted_sharethis_terms' => 'Y',
			) );

			// Hide the notice for now, it will disappear upon reload.
			$html_share_buttons_form .= '#sharethis_terms_notice { display: none }.ssbp-facebook_save { background-color: #365397 !important; }';
		}

		// Get the font family needed.
		$html_share_buttons_form .= $this->get_font_family();

		// If left to right.
		if ( is_rtl() ) {
			// Move save button.
			$html_share_buttons_form .= '.ssba-btn-save{ left: 0!important;
											right: auto !important;
											border-radius: 0 5px 5px 0; }';
		}

		wp_add_inline_style( "{$this->plugin->assets_prefix}-admin-theme", $html_share_buttons_form );
	}
}
