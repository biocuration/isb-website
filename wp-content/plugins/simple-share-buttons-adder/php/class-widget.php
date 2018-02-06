<?php
/**
 * Forms.
 *
 * @package SimpleShareButtonsAdder
 */

namespace SimpleShareButtonsAdder;

/**
 * Widget Class
 *
 * @package SimpleShareButtonsAdder
 */
class Widget extends \WP_Widget {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		parent::__construct(
			'ssba_widget', // Base ID.
			'Share Buttons', // Name.
			array(
				'description' => __( 'Simple Share Buttons Adder', 'text_domain' ),
			)
		);

		// Add ssba to available widgets.
		add_action( 'widgets_init', create_function( '', 'register_widget( "SimpleShareButtonsAdder\Widget" );' ) );
	}

	/**
	 * Extract required arguments and run the shortcode.
	 *
	 * @param array $args The widget arguments.
	 * @param array $instance THe widget instance.
	 */
	public function widget( $args, $instance ) {
		$before_title = $args['before_title'];
		$before_widget = $args['before_widget'];
		$after_title = $args['after_title'];
		$after_widget = $args['after_widget'];
		$title = apply_filters( 'widget_title', $instance['title'] );
		$url = $instance['url'];
		$pagetitle = $instance['pagetitle'];

		echo wp_kses_post( $before_widget );

		if ( ! empty( $title ) ) {
			echo wp_kses_post( $before_title . $title . $after_title );
		}

		$shortcode = '[ssba-buttons';
		$shortcode .= '' !== $url ? ' url="' . $url . '"' : '';
		$shortcode .= '' !== $pagetitle ? ' title="' . $pagetitle . '"' : '';
		$shortcode .= ' widget="Y"]';

		echo do_shortcode( $shortcode );
		echo wp_kses_post( $after_widget );
	}

	/**
	 * Form widget function.
	 *
	 * @param array $instance THe form instance.
	 */
	public function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = esc_html__( 'Share Buttons', 'simple-share-buttons-adder' );
		}

		if ( isset( $instance['title'] ) ) {
			$url = esc_url( $instance['url'] );
		} else {
			$url = '';
		}

		if ( isset( $instance['title'] ) ) {
			$pagetitle = esc_attr( $instance['pagetitle'] );
		} else {
			$pagetitle = '';
		}

		// Title.
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'title' ) ) . '">Title:</label><input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $title ) . '" /></p>';

		// URL.
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'url' ) ) . '">URL:</label><input class="widefat" id="' . esc_attr( $this->get_field_id( 'url' ) ) . '" name="' . esc_attr( $this->get_field_name( 'url' ) ) . '" type="text" value="' . esc_attr( $url ) . '" /></p>';
		echo '<p class="description">' . esc_html__( 'Leave this blank to share the current page, or enter a URL to force one URL for all pages.', 'simple-share-buttons-adder' ) . '</p>';

		// Page title.
		echo '<p><label for="' . esc_attr( $this->get_field_id( 'pagetitle' ) ) . '">Page title:</label><input class="widefat" id="' . esc_attr( $this->get_field_id( 'pagetitle' ) ) . '" name="' . esc_attr( $this->get_field_name( 'pagetitle' ) ) . '" type="text" value="' . esc_attr( $pagetitle ) . '" /></p>';
		echo '<p class="description">Set a page title for the page being shared, leave this blank if you have not set a URL.</p>';
	}

	/**
	 * Update the widget.
	 *
	 * @param array $new_instance The new value of the widget.
	 * @param array $old_instance The old value of the widget.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['url'] = strip_tags( $new_instance['url'] );
		$instance['pagetitle'] = strip_tags( $new_instance['pagetitle'] );

		return $instance;
	}

	/**
	 * The widget registration.
	 *
	 * @action admin_init
	 */
	public function mywidget_init() {
		wp_register_sidebar_widget( 'ssba_buttons_widget', 'Share Buttons Widget', array( $this, 'ssba_widget' ) );
		wp_register_widget_control( 'ssba_buttons_widget', 'Share Buttons Widget', array( $this, 'ssba_widget_control' ) );
	}
}
