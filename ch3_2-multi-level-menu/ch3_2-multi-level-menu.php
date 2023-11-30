<?php

/* 
Plugin Name: Ch3.2 Multi level menu creation
Plugin URI: tassawer.apphb.com 
Description: How to manage plugin option in multilevel menu
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
 */

add_action( 'admin_menu', 'ch3mlm_admin_menu' );
function ch3mlm_admin_menu() {
    // Create top-level menu item
    add_menu_page( 
            'My Complex Plugin Configuration Page', //$page_title
            'My Complex Plugin',//$menu_title
            'manage_options', // $capability
            'ch3mlm-main-menu', // $menu_slug
            'ch3mlm_my_complex_main', // $function
            plugins_url( 'myplugin.png', __FILE__ ) ); // $icon_url
 
    // Create a sub-menu under the top-level menu
    add_submenu_page( 
            'ch3mlm-main-menu', // $parent_slug
            'My Complex Menu Sub-Config Page', //$page_title
            'Sub-Config Page', //$menu_title
            'manage_options', //$capability
            'ch3mlm-sub-menu', //$menu_slug
            'ch3mlm_my_complex_submenu' ); //$function
}