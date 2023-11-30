<?php 

/* 
Plugin Name: Ch2.5 Private Menu Item
Plugin URI: tassawer.apphb.com 
Description: Add a menu item that is visible to only logged in users
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
 */

add_filter( 'wp_nav_menu_objects', 'ch2nmf_new_nav_menu_items', 10, 2 );
function ch2nmf_new_nav_menu_items( $sorted_menu_items, $args ) {
    // check if user is logged in
    if(is_user_logged_in() == FALSE) {
        // Loop through all menu item received
        foreach($sorted_menu_items as $key => $value) {
            if($value->title == "Private Area") {
                unset($sorted_menu_items[$key]);
            }
        }
    }
    
    //print_r( $sorted_menu_items );
    return $sorted_menu_items;
}