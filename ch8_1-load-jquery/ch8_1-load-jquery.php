<?php

/* 
Plugin Name: Ch8.1 Load jQuery
Plugin URI: tassawer.apphb.com 
Description: Plugin to demonstrate how to avoid jQuery conflict
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
 */

add_action( 'wp_enqueue_scripts', 'ch8lj_front_facing_pages' );
function ch8lj_front_facing_pages() {
    wp_enqueue_script( 'jquery' );
}