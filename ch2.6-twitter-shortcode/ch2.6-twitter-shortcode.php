<?php

/* 
 Plugin Name: Ch2.6 Twitter Short Code
Plugin URI: tassawer.apphb.com 
Description: How to add short code
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
 */

add_shortcode( 'tf', 'ch2ts_twitter_feed_shortcode' );
function ch2ts_twitter_feed_shortcode( $atts ) { 
    $output = '<a href="http://twitter.com/tassawer_">Twitter Feed</a>';
    return $output;
}