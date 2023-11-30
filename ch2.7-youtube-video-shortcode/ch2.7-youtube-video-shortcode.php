<?php

/* 
Plugin Name: Ch2.7 Youtube Video Embed Short Code
Plugin URI: tassawer.apphb.com 
Description: Create a parameterized short code
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
 */



register_activation_hook( __FILE__, 'ch2ye_set_default_options_array' );
function ch2ye_set_default_options_array() {
    if ( get_option( 'ch2ye_options_1' ) === false ) {
        ch2ye_create_setting( 1 );
    }
}
function ch2ye_create_setting( $option_id ) {
    $options['setting_name'] = 'Default';
    $options['width'] = 560;
    $options['height'] = 315;
    $options['show_suggestions'] = false;  
    $option_name = 'ch2ye_options_' . $option_id;
    update_option( $option_name, $options );
}

add_shortcode( 'youtubevid', 'ch2ye_youtube_embed_shortcode' );
function ch2ye_youtube_embed_shortcode( $atts ) {
    extract( shortcode_atts( array( 'id' => '', 'option_id' => '' ), $atts ) );
    
    if(empty($option_id || intval($option_id)<1 || intval($option_id)>5)) {
        $option_id = 1;
    }
    
    $option_name = 'ch2ye_options_'.intval($option_id);
    $options = get_option($option_name);
    
    /*
    $output = '<iframe width="560" height="315" '
            . 'src="http://www.youtube.com/embed/' . $id . '" '
            . 'frameborder="0" allowfullscreen></iframe>'; */
    
    $output = '<iframe width="' . $options['width'];
    $output .= '" height="' . $options['height'];
    $output .= '" src="http://www.youtube.com/embed/' . $id;
    $output .=  ( $options['show_suggestions'] == true ? "" : "?rel=0" );
    $output .= '" frameborder="0" allowfullscreen></iframe>';
    
    return $output;
}


// Assign function to be called when admin menu is constructed
add_action( 'admin_menu', 'ch2ye_settings_menu' );
// Function to add item to Settings menu and 
// specify function to display options page content
function ch2ye_settings_menu() {
    add_options_page(
            'YouTube Embed Configuration', //$page_title
            'YouTube Embed', //$menu_title
            'manage_options', //$capability
            'ch2ye-youtube-embed', //$menu_slug
            'ch2ye_config_page' ); //$function
}

// Function to display options page content
function ch2ye_config_page() {   
    // Retrieve plugin configuration options from database
    if ( isset( $_GET['option_id'] ) )
        $option_id = intval( $_GET['option_id'] );
    else
        $option_id = 1;
    
    $options = get_option( 'ch2ye_options_' . $option_id );
    if ( $options === false ) {
        ch2ye_create_setting( $option_id );
        $options = get_option( 'ch2ye_options_' . $option_id );
    } ?>
    <div id="ch2ye-general" class="wrap">
        <h2>YouTube Embed</h2>
        <!-- Display message when settings are saved --> 
        <?php if ( isset( $_GET['message'] ) && $_GET['message'] == '1' ) { ?> 
            <div id='message' class='updated fade'>
                <p><strong>Settings Saved</strong></p>
            </div>
        <?php } ?>
       
        <!-- Option selector -->
        <div id="icon-themes" class="icon32"><br></div>
        <h2 class="nav-tab-wrapper">
        <?php for ( $counter = 1; $counter <= 5; $counter++ ) {
            $temp_option_name = "ch2ye_options_" . $counter;
            $temp_options = get_option( $temp_option_name ); 
            $class = ( $counter == $option_id ) ? ' nav-tab-active' : ''; ?>
            <a class="nav-tab<?php echo $class; ?>"
               href="<?php echo add_query_arg( array( 'page' => 'ch2ye-youtube-embed', 'option_id' => $counter ), admin_url( 'options-general.php' ) ); ?>">
                <?php echo $counter; ?>
                <?php if ( $temp_options !== false ) 
                        echo ' (' . $temp_options['setting_name'] . ')';
                    else
                        echo ' (Empty)'; ?>
            </a>
        <?php } ?>
        </h2><br />   
   
        <!-- Main options form -->
        <form name="ch2ye_options_form" method="post" action="admin-post.php">
            <input type="hidden" name="action" value="save_ch2ye_options" />
            <input type="hidden" name="option_id" value="<?php echo $option_id; ?>" />
            <?php wp_nonce_field( 'ch2ye' ); ?>

            <table>
                <tr>
                    <td>Setting Name</td>
                    <td>
                        <input type="text" name="setting_name" 
                               value="<?php echo esc_html( $options['setting_name'] ); ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>Video Width</td>
                    <td>
                        <input type="text" name="width" 
                               value="<?php echo esc_html( $options['width'] ); ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>Video Height</td>
                    <td>
                        <input type="text" name="height" 
                               value="<?php echo esc_html( $options['height'] ); ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>Display suggestions after viewing</td>
                    <td>
                        <input type="checkbox" name="show_suggestions" 
                            <?php if ( $options['show_suggestions'] ) echo ' checked="checked"'; ?>/>
                    </td>
                </tr>
            </table><br />
            <input type="submit" value="Submit" class="button-primary" />
        </form>
    </div>
<?php }

// function to process user submit option in form
add_action( 'admin_init', 'ch2ye_admin_init' );
function ch2ye_admin_init() {
    // admin_post_ is default and save_ch2ye_options is form input name action
    add_action( 'admin_post_save_ch2ye_options', 'process_ch2ye_options' );
}

// Function to process user data submission
function process_ch2ye_options() {
    // Check that user has proper security level
    if ( !current_user_can( 'manage_options' ) )
        wp_die( 'Not allowed' );
   
    // Check that nonce field is present
    check_admin_referer( 'ch2ye' );
       
    // Check if option_id field was present
    if ( isset( $_POST['option_id'] ) )
        $option_id = $_POST['option_id'];
    else
        $option_id = 1;
    
    // Build option name and retrieve options
    $options_name = 'ch2ye_options_' . $option_id;
    $options = get_option( $options_name );
       
    // Cycle through all text fields and store their values
    foreach ( array( 'setting_name', 'width', 'height' ) as $param_name ) {
        if ( isset( $_POST[$param_name] ) ) {
            $options[$param_name] = $_POST[$param_name];
        }
    }
       
    // Cycle through all check box form fields and set
    // options array to true or false values
    foreach ( array( 'show_suggestions' ) as $param_name ) {
        if ( isset( $_POST[$param_name] ) ) {
            $options[$param_name] = true;
        } else {
            $options[$param_name] = false;
        }
    }
           
    // Store updated options array to database
    update_option( $options_name, $options );
    $cleanaddress = add_query_arg( array( 'message' => 1,
                              'option_id' => $option_id,
                              'page' => 'ch2ye-youtube-embed' ),
                       admin_url( 'options-general.php' ) );
    wp_redirect( $cleanaddress );
    exit;
}