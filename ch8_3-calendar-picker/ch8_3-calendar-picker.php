<?php

/* 
Plugin Name: Ch8.3 jQuery Date Picker
Plugin URI: tassawer.apphb.com 
Description: Create a pop up date picker to add date
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
 */

add_action( 'admin_enqueue_scripts', 'ch8cp_admin_scripts' );
function ch8cp_admin_scripts() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_style( 'datepickercss', plugins_url( 'jquery-ui.css', __FILE__ ), array(), '1.12' );
    
    wp_enqueue_script( 'tiptipjs',
                    plugins_url( 'tiptip/jquery.tipTip.js', __FILE__ ),
                    array(), '1.3' );
    wp_enqueue_style( 'tiptip', 
                    plugins_url('tiptip/tipTip.css', __FILE__ ),
                    array(), '1.3' );
}

add_action( 'add_meta_boxes', 'ch8cp_register_meta_box' );
function ch8cp_register_meta_box() {
    add_meta_box( 
            'ch8cp_datepicker_box', // $id
            'Assign Date', // $title
            'ch8cp_date_meta_box', // $callback
            'post', // $screen
            'normal'); //$context
}

function ch8cp_date_meta_box( $post ) { ?>
    <input type="text" class="ch8cp_tooltip" title="Please enter a date" id="ch8cp_date" name="ch8cp_date" />
    <!-- JavaScript function to display calendar button -->
    <!-- and associate date selection with field -->
    <script type='text/javascript'>
        jQuery( document ).ready( function() {
        jQuery( '#ch8cp_date' ).datepicker( 
                { minDate: '+0',
            dateFormat: 'yy-mm-dd',
            showOn: 'both',
            constrainInput: true} );
        } );
        
        jQuery( '.ch8cp_tooltip' ).each( function() {
                jQuery( this ).tipTip();
            }
        );

    </script>
<?php }