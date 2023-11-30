<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Check that file was called from WordPress admin
if( !defined('WP_UNINSTALL_PLUGIN') )
    exit();

global $wpdb;

// Check if site is configured for network installation
if (is_multisite()) {
    if ( !empty($_GET['networkwide']) ) {
        // Get blog list and cycle through all blogs
        $start_blog = $wpdb->blogid;
        $blog_list = $wpdb->get_col( 'SELECT blog_id FROM '. $wpdb->blogs);
        foreach ( $blog_list as $blog ) {
            switch_to_blog( $blog );
            // Call function to delete bug table with prefix
            ch7bt_drop_table( $wpdb->get_blog_prefix() );
        }
        switch_to_blog( $start_blog );
        return;
    }   
}
ch7bt_drop_table( $wpdb->prefix );

function ch7bt_drop_table( $prefix ) {
    global $wpdb;   
    $wpdb->query($wpdb->prepare('DROP TABLE '.$prefix. 'ch7_bug_data'));   
}