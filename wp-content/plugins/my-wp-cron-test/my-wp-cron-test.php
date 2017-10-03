<?php
/*
Plugin Name: My WP-Cron Test
*/

echo '<pre>'; print_r( _get_cron_array() ); echo '</pre>';

function bl_print_tasks() {
    echo '<pre>'; print_r( _get_cron_array() ); echo '</pre>';
}