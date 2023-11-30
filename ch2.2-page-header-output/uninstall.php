<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Check that code was called from WordPress with  
// uninstallation constant declared
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit;

// Check if options exist and delete them if present
if ( get_option( 'ch2pho_options' ) != false ) {
    delete_option( 'ch2pho_options' );
}