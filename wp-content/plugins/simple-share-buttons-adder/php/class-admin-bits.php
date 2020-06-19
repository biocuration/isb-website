<?php
/**
 * Admin Bits.
 *
 * @package SimpleShareButtonsAdder
 */

namespace SimpleShareButtonsAdder;

/**
 * Admin Bits Class
 *
 * @package SimpleShareButtonsAdder
 */
class Admin_Bits
{

    /**
     * Plugin instance.
     *
     * @var object
     */
    public $plugin;

    /**
     * Simple_Share_Buttons_Adder instance.
     *
     * @var object
     */
    public $class_ssba;

    /**
     * Database instance.
     *
     * @var object
     */
    public $database;

    /**
     * Admin Panel instance.
     *
     * @var object
     */
    public $admin_panel;

    /**
     * Class constructor.
     *
     * @param object $plugin Plugin class.
     * @param object $class_ssba Simple Share Buttons Adder class.
     * @param object $database Database class.
     * @param object $admin_panel Admin Panel class.
     */
    public function __construct($plugin, $class_ssba, $database, $admin_panel)
    {
        $this->plugin      = $plugin;
        $this->class_ssba  = $class_ssba;
        $this->database    = $database;
        $this->admin_panel = $admin_panel;
    }

    /**
     * ShareThis terms notice detector.
     *
     * @action admin_notices
     */
    public function sharethisTermsNotice()
    {
        // If the sharethis terms have not yet been accepted.
        if (isset($arr_settings['accepted_sharethis_terms'], $arr_settings['hide_share_this_terms']) && 'Y' !== $arr_settings['accepted_sharethis_terms'] && true !== $arr_settings['hide_sharethis_terms']) {
            ?>
            <div id="sharethis_terms_notice" class="update-nag notice is-dismissible">
                <p>
                    <?php echo esc_html__('There are some', 'simple-share-buttons-adder'); ?>
                    <strong><?php echo esc_html__('great new features',
                            'simple-share-buttons-adder'); ?></strong> <?php echo esc_html__('available with Simple Share Buttons Adder 6.3',
                        'simple-share-buttons-adder'); ?>,
                    <?php echo esc_html__('such as an improved mobile Facebook sharing experience and Facebook analytics.
					We\'ve updated our'); ?> <a href="http://simplesharebuttons.com/privacy"
                                                target="_blank"><?php echo esc_html__('privacy policy and terms of use',
                            'simple-share-buttons-adder'); ?></a> <?php echo esc_html__('with important changes you should review. To take advantage of the new features, please review and accept the new',
                        'simple-share-buttons-adder'); ?> <a href="http://simplesharebuttons.com/privacy"
                                                             target="_blank">terms and privacy policy</a>.
                    <a href="options-general.php?page=simple-share-buttons-adder&accept-terms=Y">
						<span class="button button-primary">
							<?php echo esc_html__('I accept', 'simple-share-buttons-adder'); ?>
						</span>
                    </a>
                </p>
            </div>
            <script type="text/javascript">
                jQuery( '#sharethis_terms_notice' ).on( 'click', '.notice-dismiss', function ( event ) {
                    jQuery.post( ajaxurl, { action: 'ssba_hide_terms' } );
                } );
            </script>
            <?php
        }
    }

    /**
     * Add settings link on plugin page.
     *
     * @filter plugin_action_links_simple-share-buttons-adder
     *
     * @param array $links The supplied links.
     *
     * @return mixed
     */
    public function ssbaSettingsLink($links)
    {
        // Add to plugins links.
        array_unshift($links,
            '<a href="options-general.php?page=simple-share-buttons-adder">' . esc_html__('Settings') . '</a>');

        return $links;
    }

    /**
     * Hides the terms agreement at user's request.
     *
     * @action wp_ajax_ssba_hide_term
     */
    public function ssbaAdminHideCallback()
    {
        $this->class_ssba->ssba_update_options(array(
            'hide_sharethis_terms' => true,
        ));
        wp_die();
    }

    /**
     * AJAX Call for adding hide option to review popup.
     *
     * @action wp_ajax_ssba_ajax_hide_review
     *
     * @param $post
     */
    public function ssbaAjaxHideReview($post)
    {
        update_option('ssba-hide-review', true);

        wp_send_json_success('hidden');
    }

    /**
     * Includes js/css files and upload script.
     *
     * @param string $hook_suffix The current admin page hook suffix.
     *
     * @action admin_enqueue_scripts
     */
    public function enqueueAdminAssets($hook_suffix)
    {
        $current_url = $this->plugin->dir_url . 'buttons/';

        if ($this->hook_suffix === $hook_suffix) {
            // All extra scripts needed.
            wp_enqueue_media();
            wp_enqueue_script('media-upload');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui');
            wp_enqueue_script("{$this->plugin->assets_prefix}-bootstrap-js");
            wp_enqueue_script("{$this->plugin->assets_prefix}-colorpicker");
            wp_enqueue_script("{$this->plugin->assets_prefix}-switch");
            wp_enqueue_script("{$this->plugin->assets_prefix}-admin");
            wp_add_inline_script("{$this->plugin->assets_prefix}-admin", sprintf('%s.boot( %s );',
                __NAMESPACE__,
                wp_json_encode(array(
                    'site'  => $current_url,
                    'nonce' => wp_create_nonce($this->plugin->meta_prefix),
                ))
            ));

            // Get sbba settings.
            $arr_settings = $this->class_ssba->get_ssba_settings();

            $custom_css = ! empty($arr_settings['ssba_additional_css']) ? $arr_settings['ssba_additional_css'] : '';
            $custom_css .= ! empty($arr_settings['ssba_plus_additional_css']) ? $arr_settings['ssba_plus_additional_css'] : '';
            $custom_css .= ! empty($arr_settings['ssba_bar_additional_css']) ? $arr_settings['ssba_bar_additional_css'] : '';

            wp_add_inline_style("{$this->plugin->assets_prefix}-admin-theme", $custom_css);

            // Admin styles.
            wp_enqueue_style("{$this->plugin->assets_prefix}-readable");
            wp_enqueue_style("{$this->plugin->assets_prefix}-colorpicker");
            wp_enqueue_style("{$this->plugin->assets_prefix}-switch");
            wp_enqueue_style("{$this->plugin->assets_prefix}-font-awesome");
            wp_enqueue_style("{$this->plugin->assets_prefix}-admin-theme");
            wp_enqueue_style("{$this->plugin->assets_prefix}-styles");
        }
    }

    /**
     * Save dismiss notice status.
     *
     * @action wp_ajax_dismiss_notice
     */
    public function dismissNotice()
    {
        check_ajax_referer($this->plugin->meta_prefix, 'nonce');

        if (! isset($_POST['type']) || '' === $_POST['type']) { // WPCS: input var okay.
            wp_send_json_error('dismiss notice failed');
        }

        $type            = sanitize_text_field(wp_unslash($_POST['type']));
        $current_notices = get_option('ssba_dismiss_notice');
        $current_notices = null !== $current_notices && false !== $current_notices && '' !== $current_notices ? $current_notices : '';

        if ('' !== $current_notices) {
            $new_notice = array_merge($current_notices, array(
                $type => false,
            ));
        } else {
            $new_notice = array(
                $type => false,
            );
        }

        update_option('ssba_dismiss_notice', $new_notice);
    }


    /**
     * Register the new simple share button adder menu dashboard link.
     *
     * @action admin_menu
     */
    public function addSsbaMenu()
    {
        $icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDIwMDEwOTA0Ly9FTiIKICJodHRwOi8vd3d3LnczLm9yZy9UUi8yMDAxL1JFQy1TVkctMjAwMTA5MDQvRFREL3N2ZzEwLmR0ZCI+CjxzdmcgdmVyc2lvbj0iMS4wIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiB3aWR0aD0iMjcyLjAwMDAwMHB0IiBoZWlnaHQ9IjIzNi4wMDAwMDBwdCIgdmlld0JveD0iMCAwIDI3Mi4wMDAwMDAgMjM2LjAwMDAwMCIKIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIG1lZXQiPgoKPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMC4wMDAwMDAsMjM2LjAwMDAwMCkgc2NhbGUoMC4xMDAwMDAsLTAuMTAwMDAwKSIKZmlsbD0iIzAwMDAwMCIgc3Ryb2tlPSJub25lIj4KPHBhdGggZD0iTTk3NSAyMjkzIGMtMTQwIC0xNiAtMjQ2IC00OCAtMzY5IC0xMTEgLTI3NSAtMTQxIC00NjEgLTM5MCAtNTI3Ci03MDcgLTE2IC03NyAtMTYgLTI4MyAwIC0zNjAgODIgLTM5NCAzNTkgLTY5MSA3MzcgLTc5MCAxNjQgLTQzIDM1MSAtNDEgNTI2CjYgMTE0IDMxIDExNSAzMiA4NyAxMTAgLTEyIDM1IC0yMyA2NSAtMjQgNjYgLTEgMiAtMjUgLTYgLTUzIC0xNyAtMTE1IC00NAotMjkxIC02MCAtNDIyIC00MCAtMjg3IDQ2IC01MjUgMjI2IC02NDkgNDkyIC01MyAxMTEgLTcwIDE4MyAtNzggMzA4IC0xNCAyNDgKNjYgNDY1IDIzNSA2NDIgMTc5IDE4OCA0MDMgMjc3IDY2MSAyNjUgNjQgLTMgMTQxIC0xMiAxNzEgLTIwIDI1MyAtNzAgNDQxCi0yMTkgNTU4IC00NDUgNDcgLTg5IDkyIC0yNDcgOTIgLTMyMCBsMCAtNDUgNTMgNyBjMjggMyA2MyA2IDc2IDYgMjMgMCAyMyAxCjE3IDYzIC0xNyAxNTIgLTkzIDM1MyAtMTgwIDQ3MiAtOTQgMTMwIC0yNTcgMjY0IC00MDAgMzMwIC0xNTIgNzEgLTM1OSAxMDYKLTUxMSA4OHoiLz4KPHBhdGggZD0iTTg3MCAyMDcwIGMtMjIxIC01OSAtNDA5IC0yMDYgLTUwOSAtNDAwIC02MiAtMTE5IC04MiAtMTk4IC04OCAtMzM1Ci01IC0xMzMgOCAtMjIwIDUwIC0zMjUgNzkgLTE5NCAyNTAgLTM2NyA0NDAgLTQ0MyAxNjcgLTY3IDM3NiAtNzUgNTM3IC0yMQpsODUgMjkgNiAxMTAgYzE2IDI2NiAxNDYgNDY4IDM3NiA1ODIgbDkyIDQ2IC01IDUxIGMtMzMgMzI1IC0yNDIgNTg5IC01NDkKNjkyIC02OCAyMiAtMTAxIDI3IC0yMjAgMzAgLTExNyAzIC0xNTMgMSAtMjE1IC0xNnogbTU4IC01MjUgYzM4IC0zMiA0OCAtNzIKMjggLTExOCAtNDUgLTEwOCAtMTk1IC03NiAtMTk2IDQxIDAgOTAgOTkgMTM1IDE2OCA3N3ogbTM3MiAxMCBjMzcgLTE5IDUwCi00MyA1MCAtOTUgMCAtNTcgLTQyIC0xMDAgLTk3IC0xMDAgLTEwMSAwIC0xNDggMTIwIC03MiAxODQgMzUgMzAgNzYgMzMgMTE5CjExeiBtLTM2MCAtNDEyIGM0MiAtMzkgNDkgLTg3IDE4IC0xMzMgLTM5IC01OCAtMTE0IC02NSAtMTY0IC0xNSAtMTggMTkgLTI0CjM1IC0yNCA3MCAwIDk1IDEwMCAxNDAgMTcwIDc4eiBtMzY5IDEzIGM1OCAtMzAgNjkgLTExOSAyMiAtMTY3IC0yMyAtMjMgLTM4Ci0yOSAtNzEgLTI5IC0zNSAwIC00OCA2IC03NyAzNSAtMzIgMzIgLTM1IDQwIC0zMCA3NiAxMSA4MSA4NSAxMjEgMTU2IDg1eiIvPgo8cGF0aCBkPSJNMTkxMiAxMjUwIGMtMTEwIC0yOSAtMTg3IC03NSAtMjczIC0xNjAgLTEyMiAtMTIwIC0xNzkgLTI1NiAtMTc5Ci00MjUgMCAtMTcwIDU3IC0zMDMgMTgxIC00MjYgMzQwIC0zNDAgOTA5IC0xNjggMTAxNCAzMDUgNjUgMjkyIC0xMDggNTkyCi0zOTcgNjkzIC0xMDIgMzUgLTI0NSA0MSAtMzQ2IDEzeiBtMTk4IC0zNjkgYzUgLTExIDEwIC00OSAxMCAtODUgbDAgLTY2IDc3CjAgYzg5IDAgMTEzIC0xNCAxMTMgLTY2IDAgLTQ4IC0yOCAtNjQgLTExNyAtNjQgbC03MyAwIDAgLTgyIGMwIC05MCAtMTQgLTExOAotNTcgLTExOCAtNTUgMCAtNjMgMTMgLTYzIDExMCBsMCA5MCAtNzMgMCBjLTg5IDAgLTExNyAxNSAtMTE3IDY1IDAgNTMgMjAgNjUKMTExIDY1IGw3OSAwIDAgNzMgYzAgNDMgNSA3OCAxMiA4NSAyMCAyMCA4NiAxNSA5OCAtN3oiLz4KPC9nPgo8L3N2Zz4K';

        add_menu_page(
            'Simple Share Buttons Adder',
            'Simple Share Buttons',
            'manage_options',
            'simple-share-buttons-adder',
            array($this, 'ssbaSettings'),
            $icon,
            26
        );
    }

    /**
     * Menu settings.
     *
     * @action admin_menu
     */
    public function ssbaMenu()
    {
        // Add menu page.
        $this->hook_suffix = add_options_page(
            esc_html__('Simple Share Buttons Adder', 'simple-share-buttons-adder'),
            esc_html__('Simple Share Buttons', 'simple-share-buttons-adder'),
            'manage_options',
            $this->plugin->assets_prefix,
            array($this, 'ssbaSettings')
        );

        // Query the db for current ssba settings.
        $arr_settings = $this->class_ssba->get_ssba_settings();

        // Get the current version.
        $version = get_option('ssba_version');

        // There was a version set.
        if (false !== $version) {
            // Check if not updated to current version.
            if ($version < SSBA_VERSION) {
                // Run the upgrade function.
                $this->database->upgrade_ssba($arr_settings, $version);
            }
        }
    }

    /**
     * Answer form.
     *
     * @return bool
     */
    public function ssbaSettings()
    {
        // Check if user has the rights to manage options.
        if ( ! current_user_can('manage_options')) {
            // Permissions message.
            wp_die(esc_html__('You do not have sufficient permissions to access this page.',
                'simple-share-buttons-adder'));
        }

        // If a post has been made.
        if (isset($_POST['ssbaData'])) { // WPCS: CSRF ok.
            // Get posted data.
            $ssba_post    = $_POST['ssbaData']; // WPCS: CSRF ok.
            $selected_tab = isset($_POST['ssba_selected_tab']) ? sanitize_text_field(wp_unslash($_POST['ssba_selected_tab'])) : ''; // WPCS: CSRF ok.

            parse_str($ssba_post, $ssba_post);

            // If the nonce doesn't check out.
            if ( ! isset($ssba_post['ssba_save_nonce']) || ! wp_verify_nonce($ssba_post['ssba_save_nonce'],
                    'ssba_save_settings')) {
                die(esc_html__('There was no nonce provided, or the one provided did not verify.',
                    'simple-share-buttons-adder'));
            }

            // Prepare array.
            $arr_options = array(
                'ssba_image_set'               => $ssba_post['ssba_image_set'],
                'ssba_size'                    => $ssba_post['ssba_size'],
                'ssba_omit_pages'              => (isset($ssba_post['ssba_omit_pages']) ? $ssba_post['ssba_omit_pages'] : ''),
                'ssba_omit_pages_plus'         => (isset($ssba_post['ssba_omit_pages_plus']) ? $ssba_post['ssba_omit_pages_plus'] : ''),
                'ssba_omit_pages_bar'          => (isset($ssba_post['ssba_omit_pages_bar']) ? $ssba_post['ssba_omit_pages_bar'] : ''),
                'ssba_pages'                   => (isset($ssba_post['ssba_pages']) ? $ssba_post['ssba_pages'] : 'N'),
                'ssba_posts'                   => (isset($ssba_post['ssba_posts']) ? $ssba_post['ssba_posts'] : 'N'),
                'ssba_cats_archs'              => (isset($ssba_post['ssba_cats_archs']) ? $ssba_post['ssba_cats_archs'] : 'N'),
                'ssba_homepage'                => (isset($ssba_post['ssba_homepage']) ? $ssba_post['ssba_homepage'] : 'N'),
                'ssba_excerpts'                => (isset($ssba_post['ssba_excerpts']) ? $ssba_post['ssba_excerpts'] : 'N'),
                'ssba_plus_pages'              => (isset($ssba_post['ssba_plus_pages']) ? $ssba_post['ssba_plus_pages'] : 'N'),
                'ssba_plus_posts'              => (isset($ssba_post['ssba_plus_posts']) ? $ssba_post['ssba_plus_posts'] : 'N'),
                'ssba_plus_cats_archs'         => (isset($ssba_post['ssba_plus_cats_archs']) ? $ssba_post['ssba_plus_cats_archs'] : 'N'),
                'ssba_plus_homepage'           => (isset($ssba_post['ssba_plus_homepage']) ? $ssba_post['ssba_plus_homepage'] : 'N'),
                'ssba_plus_excerpts'           => (isset($ssba_post['ssba_plus_excerpts']) ? $ssba_post['ssba_plus_excerpts'] : 'N'),
                'ssba_bar_pages'               => (isset($ssba_post['ssba_bar_pages']) ? $ssba_post['ssba_bar_pages'] : 'N'),
                'ssba_bar_posts'               => (isset($ssba_post['ssba_bar_posts']) ? $ssba_post['ssba_bar_posts'] : 'N'),
                'ssba_bar_cats_archs'          => (isset($ssba_post['ssba_bar_cats_archs']) ? $ssba_post['ssba_bar_cats_archs'] : 'N'),
                'ssba_bar_homepage'            => (isset($ssba_post['ssba_bar_homepage']) ? $ssba_post['ssba_bar_homepage'] : 'N'),
                'ssba_bar_excerpts'            => (isset($ssba_post['ssba_bar_excerpts']) ? $ssba_post['ssba_bar_excerpts'] : 'N'),
                'ssba_align'                   => (isset($ssba_post['ssba_align']) ? $ssba_post['ssba_align'] : 'N'),
                'ssba_plus_align'              => (isset($ssba_post['ssba_plus_align']) ? $ssba_post['ssba_plus_align'] : 'N'),
                'ssba_padding'                 => $ssba_post['ssba_padding'],
                'ssba_before_or_after'         => $ssba_post['ssba_before_or_after'],
                'ssba_before_or_after_plus'    => $ssba_post['ssba_before_or_after_plus'],
                'ssba_additional_css'          => $ssba_post['ssba_additional_css'],
                'ssba_custom_styles'           => $ssba_post['ssba_custom_styles'],
                'ssba_custom_styles_enabled'   => $ssba_post['ssba_custom_styles_enabled'],
                'ssba_email_message'           => stripslashes_deep($ssba_post['ssba_email_message']),
                'ssba_twitter_text'            => stripslashes_deep($ssba_post['ssba_twitter_text']),
                'ssba_buffer_text'             => stripslashes_deep($ssba_post['ssba_buffer_text']),
                'ssba_flattr_user_id'          => stripslashes_deep($ssba_post['ssba_flattr_user_id']),
                'ssba_flattr_url'              => stripslashes_deep($ssba_post['ssba_flattr_url']),
                'ssba_share_new_window'        => (isset($ssba_post['ssba_share_new_window']) ? $ssba_post['ssba_share_new_window'] : 'N'),
                'ssba_link_to_ssb'             => (isset($ssba_post['ssba_link_to_ssb']) ? $ssba_post['ssba_link_to_ssb'] : 'N'),
                'ssba_show_share_count'        => (isset($ssba_post['ssba_show_share_count']) ? $ssba_post['ssba_show_share_count'] : 'N'),
                'ssba_share_count_style'       => $ssba_post['ssba_share_count_style'],
                'ssba_share_count_css'         => $ssba_post['ssba_share_count_css'],
                'ssba_share_count_once'        => (isset($ssba_post['ssba_share_count_once']) ? $ssba_post['ssba_share_count_once'] : 'N'),
                'ssba_widget_text'             => $ssba_post['ssba_widget_text'],
                'ssba_rel_nofollow'            => (isset($ssba_post['ssba_rel_nofollow']) ? $ssba_post['ssba_rel_nofollow'] : 'N'),
                'ssba_default_pinterest'       => (isset($ssba_post['ssba_default_pinterest']) ? $ssba_post['ssba_default_pinterest'] : 'N'),
                'ssba_pinterest_featured'      => (isset($ssba_post['ssba_pinterest_featured']) ? $ssba_post['ssba_pinterest_featured'] : 'N'),
                'ssba_content_priority'        => (isset($ssba_post['ssba_content_priority']) ? $ssba_post['ssba_content_priority'] : 'N'),
                'ssba_plus_additional_css'     => $ssba_post['ssba_plus_additional_css'],
                'ssba_plus_email_message'      => stripslashes_deep($ssba_post['ssba_plus_email_message']),
                'ssba_plus_twitter_text'       => stripslashes_deep($ssba_post['ssba_plus_twitter_text']),
                'ssba_plus_buffer_text'        => stripslashes_deep($ssba_post['ssba_plus_buffer_text']),
                'ssba_plus_flattr_user_id'     => stripslashes_deep($ssba_post['ssba_plus_flattr_user_id']),
                'ssba_plus_flattr_url'         => stripslashes_deep($ssba_post['ssba_plus_flattr_url']),
                'ssba_plus_share_new_window'   => (isset($ssba_post['ssba_plus_share_new_window']) ? $ssba_post['ssba_plus_share_new_window'] : 'N'),
                'ssba_plus_link_to_ssb'        => (isset($ssba_post['ssba_plus_link_to_ssb']) ? $ssba_post['ssba_plus_link_to_ssb'] : 'N'),
                'ssba_plus_show_share_count'   => (isset($ssba_post['ssba_plus_show_share_count']) ? $ssba_post['ssba_plus_show_share_count'] : 'N'),
                'ssba_plus_share_count_style'  => $ssba_post['ssba_plus_share_count_style'],
                'ssba_plus_share_count_css'    => $ssba_post['ssba_plus_share_count_css'],
                'ssba_plus_share_count_once'   => (isset($ssba_post['ssba_plus_share_count_once']) ? $ssba_post['ssba_plus_share_count_once'] : 'N'),
                'ssba_plus_widget_text'        => $ssba_post['ssba_plus_widget_text'],
                'ssba_plus_rel_nofollow'       => (isset($ssba_post['ssba_plus_rel_nofollow']) ? $ssba_post['ssba_plus_rel_nofollow'] : 'N'),
                'ssba_plus_default_pinterest'  => (isset($ssba_post['ssba_plus_default_pinterest']) ? $ssba_post['ssba_plus_default_pinterest'] : 'N'),
                'ssba_plus_pinterest_featured' => (isset($ssba_post['ssba_plus_pinterest_featured']) ? $ssba_post['ssba_plus_pinterest_featured'] : 'N'),
                'ssba_bar_additional_css'      => $ssba_post['ssba_bar_additional_css'],
                'ssba_bar_email_message'       => stripslashes_deep($ssba_post['ssba_bar_email_message']),
                'ssba_bar_twitter_text'        => stripslashes_deep($ssba_post['ssba_bar_twitter_text']),
                'ssba_bar_buffer_text'         => stripslashes_deep($ssba_post['ssba_bar_buffer_text']),
                'ssba_bar_flattr_user_id'      => stripslashes_deep($ssba_post['ssba_bar_flattr_user_id']),
                'ssba_bar_flattr_url'          => stripslashes_deep($ssba_post['ssba_bar_flattr_url']),
                'ssba_bar_share_new_window'    => (isset($ssba_post['ssba_bar_share_new_window']) ? $ssba_post['ssba_bar_share_new_window'] : 'N'),
                'ssba_bar_link_to_ssb'         => (isset($ssba_post['ssba_bar_link_to_ssb']) ? $ssba_post['ssba_bar_link_to_ssb'] : 'N'),
                'ssba_bar_show_share_count'    => (isset($ssba_post['ssba_bar_show_share_count']) ? $ssba_post['ssba_bar_show_share_count'] : 'N'),
                'ssba_bar_share_count_style'   => $ssba_post['ssba_bar_share_count_style'],
                'ssba_bar_share_count_css'     => $ssba_post['ssba_bar_share_count_css'],
                'ssba_bar_share_count_once'    => (isset($ssba_post['ssba_bar_share_count_once']) ? $ssba_post['ssba_bar_share_count_once'] : 'N'),
                'ssba_bar_widget_text'         => $ssba_post['ssba_bar_widget_text'],
                'ssba_bar_rel_nofollow'        => (isset($ssba_post['ssba_bar_rel_nofollow']) ? $ssba_post['ssba_bar_rel_nofollow'] : 'N'),
                'ssba_bar_default_pinterest'   => (isset($ssba_post['ssba_bar_default_pinterest']) ? $ssba_post['ssba_bar_default_pinterest'] : 'N'),
                'ssba_bar_pinterest_featured'  => (isset($ssba_post['ssba_bar_pinterest_featured']) ? $ssba_post['ssba_bar_pinterest_featured'] : 'N'),

                // Share container.
                'ssba_div_padding'             => $ssba_post['ssba_div_padding'],
                'ssba_div_rounded_corners'     => (isset($ssba_post['ssba_div_rounded_corners']) ? $ssba_post['ssba_div_rounded_corners'] : 'N'),
                'ssba_border_width'            => $ssba_post['ssba_border_width'],
                'ssba_div_border'              => $ssba_post['ssba_div_border'],
                'ssba_div_background'          => $ssba_post['ssba_div_background'],

                // Text.
                'ssba_share_text'              => stripslashes_deep($ssba_post['ssba_share_text']),
                'ssba_text_placement'          => $ssba_post['ssba_text_placement'],
                'ssba_font_family'             => $ssba_post['ssba_font_family'],
                'ssba_font_color'              => $ssba_post['ssba_font_color'],
                'ssba_font_size'               => $ssba_post['ssba_font_size'],
                'ssba_font_weight'             => $ssba_post['ssba_font_weight'],
                'ssba_plus_share_text'         => stripslashes_deep($ssba_post['ssba_plus_share_text']),
                'ssba_plus_text_placement'     => $ssba_post['ssba_plus_text_placement'],
                'ssba_plus_font_family'        => $ssba_post['ssba_plus_font_family'],
                'ssba_plus_font_color'         => $ssba_post['ssba_plus_font_color'],
                'ssba_plus_font_size'          => $ssba_post['ssba_plus_font_size'],
                'ssba_plus_font_weight'        => $ssba_post['ssba_plus_font_weight'],

                // Included buttons.
                'ssba_selected_buttons'        => $ssba_post['ssba_selected_buttons'],
                'ssba_selected_bar_buttons'    => $ssba_post['ssba_selected_bar_buttons'],
                'ssba_selected_plus_buttons'   => $ssba_post['ssba_selected_plus_buttons'],
                'ssba_plus_button_style'       => $ssba_post['ssba_plus_button_style'],
                'ssba_bar_style'               => $ssba_post['ssba_bar_style'],
                'ssba_new_buttons'             => $ssba_post['ssba_new_buttons'],
                'ssba_bar_enabled'             => $ssba_post['ssba_bar_enabled'],
                'ssba_bar_position'            => $ssba_post['ssba_bar_position'],
                'ssba_plus_height'             => $ssba_post['ssba_plus_height'],
                'ssba_plus_width'              => $ssba_post['ssba_plus_width'],
                'ssba_plus_margin'             => $ssba_post['ssba_plus_margin'],
                'ssba_plus_button_color'       => $ssba_post['ssba_plus_button_color'],
                'ssba_plus_button_hover_color' => $ssba_post['ssba_plus_button_hover_color'],
                'ssba_plus_icon_size'          => $ssba_post['ssba_plus_icon_size'],
                'ssba_plus_icon_color'         => $ssba_post['ssba_plus_icon_color'],
                'ssba_plus_icon_hover_color'   => $ssba_post['ssba_plus_icon_hover_color'],
                'ssba_bar_height'              => $ssba_post['ssba_bar_height'],
                'ssba_bar_width'               => $ssba_post['ssba_bar_width'],
                'ssba_bar_button_color'        => $ssba_post['ssba_bar_button_color'],
                'ssba_bar_button_hover_color'  => $ssba_post['ssba_bar_button_hover_color'],
                'ssba_bar_icon_size'           => $ssba_post['ssba_bar_icon_size'],
                'ssba_bar_icon_color'          => $ssba_post['ssba_bar_icon_color'],
                'ssba_bar_icon_hover_color'    => $ssba_post['ssba_bar_icon_hover_color'],
                'ssba_bar_desktop'             => isset($ssba_post['ssba_bar_desktop']) ? $ssba_post['ssba_bar_desktop'] : 'N',
                'ssba_bar_margin'              => $ssba_post['ssba_bar_margin'],
                'ssba_bar_mobile'              => isset($ssba_post['ssba_bar_mobile']) ? $ssba_post['ssba_bar_mobile'] : 'N',
                'ssba_mobile_breakpoint'       => $ssba_post['ssba_mobile_breakpoint'],
                'ssba_custom_facebook'         => $ssba_post['ssba_custom_facebook'],
                'ssba_custom_twitter'          => $ssba_post['ssba_custom_twitter'],
                'ssba_custom_linkedin'         => $ssba_post['ssba_custom_linkedin'],
                'ssba_custom_flattr'           => $ssba_post['ssba_custom_flattr'],
                'ssba_custom_pinterest'        => $ssba_post['ssba_custom_pinterest'],
                'ssba_custom_print'            => $ssba_post['ssba_custom_print'],
                'ssba_custom_reddit'           => $ssba_post['ssba_custom_reddit'],
                'ssba_custom_stumbleupon'      => $ssba_post['ssba_custom_stumbleupon'],
                'ssba_custom_tumblr'           => $ssba_post['ssba_custom_tumblr'],
                'ssba_custom_vk'               => $ssba_post['ssba_custom_vk'],
                'ssba_custom_whatsapp'         => $ssba_post['ssba_custom_whatsapp'],
                'ssba_custom_xing'             => $ssba_post['ssba_custom_xing'],
                'ssba_custom_yummly'           => $ssba_post['ssba_custom_yummly'],
                'ssba_custom_email'            => $ssba_post['ssba_custom_email'],
                'ssba_custom_buffer'           => $ssba_post['ssba_custom_buffer'],
                'ssba_custom_diggit'           => $ssba_post['ssba_custom_diggit'],
                'ssba_custom_facebook_save'    => $ssba_post['ssba_custom_facebook_save'],

                // Shared count.
                'sharedcount_enabled'          => isset($ssba_post['sharedcount_enabled']) ? $ssba_post['sharedcount_enabled'] : 'N',
                'sharedcount_api_key'          => $ssba_post['sharedcount_api_key'],
                'sharedcount_plan'             => $ssba_post['sharedcount_plan'],

                // Facebook.
                'facebook_insights'            => $ssba_post['facebook_insights'],
                'facebook_app_id'              => $ssba_post['facebook_app_id'],
                'ignore_facebook_sdk'          => $ssba_post['ignore_facebook_sdk'],
                'plus_facebook_insights'       => $ssba_post['plus_facebook_insights'],
                'plus_facebook_app_id'         => $ssba_post['plus_facebook_app_id'],
                'plus_ignore_facebook_sdk'     => $ssba_post['plus_ignore_facebook_sdk'],
                'bar_facebook_insights'        => $ssba_post['bar_facebook_insights'],
                'bar_facebook_app_id'          => $ssba_post['bar_facebook_app_id'],
            );

            // Save the settings.
            $this->class_ssba->ssba_update_options($arr_options);

            // Save selected tab.
            update_option('ssba_selected_tab', $selected_tab);

            // Return success.
            return true;
        } // End if().

        // Query the db for current ssba settings.
        $arr_settings = $this->class_ssba->get_ssba_settings();

        // Admin panel.
        $this->admin_panel->admin_panel($arr_settings);
    }
}
