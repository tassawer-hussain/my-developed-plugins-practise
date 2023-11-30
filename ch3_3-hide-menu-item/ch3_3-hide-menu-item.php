<?php

/* 
Plugin Name: Ch3.3 Hide Menu Items
Plugin URI: tassawer.apphb.com 
Description: How to hide menu items from some users
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
 */

if(is_admin()) {
    require plugin_dir_path(__FILE__).'hide-menu-item-admin-functions.php';
}