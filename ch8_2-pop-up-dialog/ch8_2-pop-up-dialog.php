<?php

/* 
Plugin Name: Ch8.2 jQuery Pop Up Dialog
Plugin URI: tassawer.apphb.com 
Description: Create a pop up dialog to show some message to site visitors
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
 */


/* to display the dialouge on specific page or on only home page
if(is_front_page()) {
    add_action( 'wp_enqueue_scripts', 'ch8pud_load_scripts' );
    add_action( 'wp_footer', 'ch8pud_footer_code' );
} */

add_action( 'wp_enqueue_scripts', 'ch8pud_load_scripts' );
function ch8pud_load_scripts() {
    // only load scripts if variable is set to true
    global $load_scripts;
    
    if($load_scripts) {
        wp_enqueue_script( 'jquery' );
        add_thickbox();
    }
}

add_action( 'wp_footer', 'ch8pud_footer_code' );
function ch8pud_footer_code() { 
    // Only load scripts if keyword is found on page
    global $load_scripts;
    if($load_scripts) { ?>
        <script type="text/javascript">
            jQuery( document ).ready(function() {
                setTimeout( function() {
                    // append after the width value, &modal=true to not display tab close button
                    tb_show( 'Pop-Up Message', '<?php echo plugins_url('content.html?width=420&height=220', __FILE__ )?>', null );
                }, 2000 );
            } );
        </script>
    <?php }
}

add_filter( 'the_posts', 'ch8pud_conditionally_add_scripts_and_styles' );
function ch8pud_conditionally_add_scripts_and_styles( $posts ) {
    // Exit function immediately if no posts are present
    if ( empty( $posts ) ) return $posts;
    
    // Global variable to indicate if scripts should be loaded
    global $load_scripts;
    $load_scripts = false;
    
    // Cycle through posts and set flag true if keyword is found
    foreach ( $posts as $post ) {       
        $shortcode_pos = stripos($post->post_content, '[popup]', 0 );
        if ( $shortcode_pos !== false ) {
            $load_scripts = true;
            return $posts;
        }
    }
    // Return posts array unchanged
    return $posts;   
}

add_shortcode( 'popup', 'ch8pud_popup_shortcode' );
function ch8pud_popup_shortcode() {
    return;   
}