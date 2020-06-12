<?php // Opening PHP tag - nothing should be before this, not even whitespace

// Customize login page with ISB logo
function my_login_logo() { ?>
    <style type="text/css">
        .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/isblogologin.png);
            padding-bottom: 30px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

// Link logo to ISB website
function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'Your Site Name and Info';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );


// Remove "lost password" from login page (safer!)
// function remove_lostpassword_text ( $text ) {
//      if ($text == 'Lost your password?'){$text = '';}
//         return $text;
//      }
// add_filter( 'gettext', 'remove_lostpassword_text' );


// update_option( 'siteurl', 'https://www.biocuration.org' );
// update_option( 'home', 'https://www.biocuration.org' );

/**
 * Removes angle brackets (characters < and >) arounds URLs in a given string
 *
 * @param string $string    The string to remove potential angle brackets from
 *
 * @return string    $string where any angle brackets surrounding an URL have been removed.
 * 
 * From: https://wordpress.stackexchange.com/questions/246377/missing-url-in-password-reset-email
 * Addresses: https://github.com/biocuration/isb-website/issues/52
 */
function remove_angle_brackets_around_url($string)
{
    return preg_replace('/<(' . preg_quote(network_site_url(), '/') . '[^>]*)>/', '\1', $string);
}

// Apply the remove_angle_brackets_around_url() function on the "retrieve password" message:
add_filter('retrieve_password_message', 'remove_angle_brackets_around_url', 99, 1);
