<?php

/* 
Plugin Name: Ch5.2 Hide custom fields
Plugin URI: tassawer.apphb.com 
Description: How to hide custom fields metabox from post and pages editors
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
 */

add_action( 'add_meta_boxes', 'ch5_hcf_remove_custom_fields_metabox' );
function ch5_hcf_remove_custom_fields_metabox() {
    // remove_meta_box($id, $screen, $context)
    remove_meta_box( 'postcustom', 'post', 'normal' );
    remove_meta_box( 'postcustom', 'page', 'normal' );
}