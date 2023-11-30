<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

add_action( 'admin_menu', 'ch3hmi_hide_menu_item' );
function ch3hmi_hide_menu_item() {
    //remove_menu_page($menu_slug);
    remove_menu_page( 'link-manager.php' );
    
    // remove_submenu_page($menu_slug, $submenu_slug)
    remove_submenu_page( 'options-general.php', 'options-permalink.php' );
}