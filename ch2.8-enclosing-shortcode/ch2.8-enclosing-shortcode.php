<?php

/* 
Plugin Name: Ch2.8 Encosing Short Code
Plugin URI: tassawer.apphb.com 
Description: Create a enclosing short code
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
*/

add_shortcode( 'private', 'ch2pit_private_shortcode' );
function ch2pit_private_shortcode( $atts, $content = null ) {
    if ( is_user_logged_in() )
        return '<div class="private">' . $content . '</div>';
    else
        return '';
}

/*
add_action( 'wp_enqueue_scripts', 'ch2pit_queue_stylesheet' );
function ch2pit_queue_stylesheet() {
    wp_enqueue_style( 'privateshortcodestyle', plugins_url( 'stylesheet.css', __FILE__ ) );
}*/

register_activation_hook( __FILE__, 'ch2pit_set_default_options_array' );
function ch2pit_set_default_options_array() {
    if ( get_option( 'ch2pit_options' ) === false ) {
        $stylesheet_location = plugin_dir_path( __FILE__ ) . 'stylesheet.css';
        $options['stylesheet'] = file_get_contents( $stylesheet_location );
        update_option( 'ch2pit_options', $options );
    }
}

add_action( 'admin_menu', 'ch2pit_settings_menu' );
function ch2pit_settings_menu() {
    add_options_page(
            'Private Item Text Configuration', //$page_title
            'Private Item Text', //$menu_title
            'manage_options', //$capability
            'ch2pit-private-item-text', //$menu_slug
            'ch2pit_config_page'); // $function
}

function ch2pit_config_page() {
    // Retrieve plugin configuration options from database
    $options = get_option( 'ch2pit_options' ); ?>
    <div id="ch2pit-general" class="wrap">
        <h2>Private Item Text</h2>
        <!-- Code to display confirmation messages when settings are saved or reset -->
        <?php if ( isset( $_GET['message'] ) && $_GET['message'] == '1' ) { ?>
            <div id='message' class='updated fade'>
                <p><strong>Settings Saved</strong></p>
            </div>
            <?php } elseif ( isset( $_GET['message'] ) && $_GET['message'] == '2' ) { ?>
            <div id='message' class='updated fade'>
                <p><strong>Stylesheet reverted to original</strong></p>
            </div>
        <?php } ?>
        <form name="ch2pit_options_form" method="post" action="admin-post.php">
            <input type="hidden" name="action" value="save_ch2pit_options" />
            <?php wp_nonce_field('ch2pit'); ?>
            Stylesheet<br />
            <textarea name="stylesheet" rows="10" cols="40" 
                      style="font-family:Consolas,Monaco,monospace">
                    <?php echo esc_html( $options['stylesheet'] ); ?>
            </textarea><br />
            <input type="submit" value="Submit" class="button-primary" />
            <input type="submit" value="Reset" name="resetstyle" class="button-primary" />
        </form>
    </div>
<?php }

add_action( 'admin_init', 'ch2pit_admin_init' );
function ch2pit_admin_init() {
    add_action( 'admin_post_save_ch2pit_options', 'process_ch2pit_options' );
}
function process_ch2pit_options() {
    // Check that user has proper security level
    if ( !current_user_can( 'manage_options' ) )
        wp_die( 'Not allowed' );
    
    // Check that nonce field created in configuration form is present
    check_admin_referer( 'ch2pit' );
    
    // Retrieve original plugin options array
    $options = get_option( 'ch2pit_options' );
    
    if ( isset( $_POST['resetstyle'] ) ) {
        // plugin_dir_path($file)
        $stylesheet_location = plugin_dir_path( __FILE__ ) . 'stylesheet.css';
        $options['stylesheet'] = file_get_contents( $stylesheet_location );
        $message = 2;
    } else {
        // Cycle through all fields and store their values in the options array
        foreach ( array( 'stylesheet' ) as $option_name ) {
            if ( isset( $_POST[$option_name] ) ) {
                $options[$option_name] = $_POST[$option_name];
            }
        }
        $message = 1;
    }
    
    // Store updated options array to database
    update_option( 'ch2pit_options', $options );
    
    // Redirect the page to the configuration form that was processed
    wp_redirect( add_query_arg( array(
        'page' => 'ch2pit-private-item-text',
        'message' => $message ),
            admin_url( 'options-general.php' ) ) );
    exit;
}

add_action( 'wp_head', 'ch2pit_page_header_output' );
function ch2pit_page_header_output() { ?>
    <style type='text/css'>
    <?php
        $options = get_option( 'ch2pit_options' );
        echo $options['stylesheet'];
    ?>
    </style>
<?php }