<?php

/* 
Plugin Name: Ch10.1 Hello World in French
Plugin URI: tassawer.apphb.com 
Description: How to write plugin for different languages
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
 */

register_activation_hook( __FILE__, 'ch10hw_set_default_options_array' );
function ch10hw_set_default_options_array() {
    if ( false === get_option( 'ch10hw_options' ) ) {
        $new_options = array();
        $new_options['default_text'] = __( 'Hello World', 'ch10hw_hello_world' );
        add_option( 'ch10hw_options', $new_options );
    }
}

add_action( 'admin_menu', 'ch10hw_settings_menu' );
function ch10hw_settings_menu() {
    add_options_page(
        __( 'Hello World Configuration', 'ch10hw_hello_world' ), // $page_title
        __( 'Hello World', 'ch10hw_hello_world' ), // $menu_title
        'manage_options', // $capability
        'ch10hw-hello-world', // $menu_slug
        'ch10hw_config_page' ); // $function
}
function ch10hw_config_page() {
    $options = get_option( 'ch10hw_options' ); ?>
    <div id="ch10hw-general" class="wrap">
        <!-- Echo translation for "Hello World" to the browser -->
        <h2><?php _e( 'Hello World', 'ch10hw_hello_world' ); ?></h2>
        <form method="post" action="admin-post.php">
            <input type="hidden" name="action"
                value="save_ch10hw_options" />
            <?php wp_nonce_field( 'ch10hw' ); ?>
            <!-- Echo translation for "Hello World" to the browser -->
            <?php _e( 'Default Text', 'ch10hw_hello_world' ); ?>:
            <input type="text" name="default_text"
                   value="<?php echo esc_html( $options['default_text']); ?>"/>
            <br />
            <input type="submit" value="<?php _e( 'Submit', 'ch10hw_hello_world' ); ?>" class="button-primary"/>
        </form>
    </div>
<?php }

// executed when the administration panel is being prepared to be displayed
add_action( 'admin_init', 'ch10hw_admin_init' );
function ch10hw_admin_init() {
     add_action( 'admin_post_save_ch10hw_options', 'process_ch10hw_options' );
}

function process_ch10hw_options() {
    if ( !current_user_can( 'manage_options' ) ) 
         wp_die( 'Not allowed' );
       
    check_admin_referer( 'ch10hw' );
    $options = get_option( 'ch10hw_options' );
  
    $options['default_text'] = $_POST['default_text'];
    update_option( 'ch10hw_options', $options );
    wp_redirect( add_query_arg( 'page', 'ch10hw-hello-world', admin_url( 'options-general.php' ) ) ); 
    exit;
}

add_shortcode( 'hello-world', 'ch10hw_hello_world_shortcode' );
function ch10hw_hello_world_shortcode() {
    $options = get_option( 'ch10hw_options' );
    
    $output = sprintf(__('The current text string is: %s.','ch10hw_hello_world'), $options['default_text'] );
    return $output;
}

//  called when the plugin is initialized
add_action( 'init', 'ch10hw_plugin_init' );
function ch10hw_plugin_init() {
    load_plugin_textdomain( 'ch10hw_hello_world',
                            false,
                            dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}