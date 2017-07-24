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
function remove_lostpassword_text ( $text ) {
     if ($text == 'Lost your password?'){$text = '';}
        return $text;
     }
add_filter( 'gettext', 'remove_lostpassword_text' );



update_option( 'siteurl', 'http://biocuration.org' );
update_option( 'home', 'http://biocuration.org' );