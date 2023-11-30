<?php

/* 
Plugin Name: Ch2.9 Object Oriented Encosing Short Code
Plugin URI: tassawer.apphb.com 
Description: Create a enclosing short code using Object Oriented approach
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
*/

class ch2_OO_Private_Item_Text {
    function __construct() {
        add_shortcode( 'private', array($this, 'ch2pit_private_shortcode' ));
        add_action( 'wp_enqueue_scripts', array($this, 'ch2pit_queue_stylesheet' ));
    }
    
    function ch2pit_private_shortcode( $atts, $content = null ) {
        if ( is_user_logged_in() )
            return '<div class="private">' . $content . '</div>';
        else
            return '';
    }

    function ch2pit_queue_stylesheet() {
        wp_enqueue_style( 'privateshortcodestyle', plugins_url( 'stylesheet.css', __FILE__ ) );
    }

}

$clas_obj = new ch2_OO_Private_Item_Text();

