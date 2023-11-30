<?php

/* 
Plugin Name: Ch3.4 Setting API
Plugin URI: tassawer.apphb.com 
Description: Use of settings api to process plugin settings
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
 */

define( "VERSION", "1.1" );

register_activation_hook( __FILE__, 'ch3sapi_set_default_options' );
function ch3sapi_set_default_options() {
    if ( get_option( 'ch3sapi_options' ) === false ) {
        $new_options['ga_account_name'] = "UA-000000-0";
        $new_options['track_outgoing_links'] = false;
        $new_options['version'] = VERSION;
        add_option( 'ch3sapi_options', $new_options );
    }
}

add_action( 'admin_init', 'ch3sapi_admin_init' );
function ch3sapi_admin_init() {
    // Register a setting group with a validation function
    // so that post data handling is done automatically for us
    register_setting( 
            'ch3sapi_settings', //$option_group, UNIQUE NAME
            'ch3sapi_options', //$option_name, SAME AS IN DATABASE
            'ch3sapi_validate_options' ); //$args, CALL BACK VALIDATING FUNC 

    // Add a new settings section within the group
    add_settings_section( 
            'ch3sapi_main_section', //$id, unique name
            'Main Settings', //$title
            'ch3sapi_main_setting_section_callback',//$callback
            'ch3sapi_settings_section' );//$page
    
    // Add each field with its name and function to use for
    // our new settings, put them in our new section
    add_settings_field( 
            'ga_account_name', //$id
            'Account Name', //$title, a label that will be display next to the field
            'ch3sapi_display_text_field', //$callback
            'ch3sapi_settings_section', //$page
            'ch3sapi_main_section', //$section
            array( 'name' => 'ga_account_name' ) ); //$args
    add_settings_field( 
            'track_outgoing_links',//$id
            'Track Outgoing Links', //$title
            'ch3sapi_display_check_box', //$callback
            'ch3sapi_settings_section',//$page
            'ch3sapi_main_section', //$section
            array( 'name' => 'track_outgoing_links' ) );//$args
    /*add_settings_field( // for dispaye select list
            'Select_List',
            'Select List',
            'ch3sapi_select_list',
            'ch3sapi_settings_section',
            'ch3sapi_main_section',
            array( 
                'name' => 'Select_List',
                'choices' => array( 'First', 'Second', 'Third' ) ) );
    add_settings_field( // for display textarea
            'text_box',//$id
            'Text Area/Box', //$title
            'ch3sapi_display_text_area', //$callback
            'ch3sapi_settings_section',//$page
            'ch3sapi_main_section', //$section
            array( 'name' => 'text_area' ) );//$args 
    */
} /*
function ch3sapi_display_text_area( $data = array() ) {
    extract ( $data );
    $options = get_option( 'ch3sapi_options' );  ?>
    <textarea type="text"
              name="ch3sapi_options[<?php echo $name; ?>]"  
              rows="5" cols="30">
        <?php if(isset($options[$name])) { echo esc_html($options[$name]); } ?></textarea>
<?php }

function ch3sapi_select_list( $data = array() ) {
    extract ( $data );
    $options = get_option( 'ch3sapi_options' ); ?>
    <select name="ch3sapi_options[<?php echo $name; ?>]'> 
        <?php foreach( $choices as $item ) { ?>
        <option value="<?php echo $item; ?>"
        <?php if (isset($options[$name])) {selected( $options[$name] == $item ); } ?>>
        <?php echo $item; ?></option>; 
    <?php } ?>
  </select> 
<?php } */

// Declare a body for the ch3sapi_validate_options function
function ch3sapi_validate_options( $input ) {
    $input['version'] = VERSION;
    return $input;
}

// Declare a body for the ch3sapi_main_setting_section_callback function
function ch3sapi_main_setting_section_callback() { ?>
    <p>This is the main configuration section.</p>
<?php }

// Provide an implementation for the ch3sapi_display_text_field function
function ch3sapi_display_text_field( $data = array() ) {
    extract( $data );
    $options = get_option( 'ch3sapi_options' ); ?>
    <input type="text" 
           name="ch3sapi_options[<?php echo $name; ?>]"
           value="<?php echo esc_html( $options[$name] ); ?>"/><br />
<?php }

// Declare and define the ch3sapi_display_check_box function
function ch3sapi_display_check_box( $data = array() ) {
    extract ( $data );
    $options = get_option( 'ch3sapi_options' ); ?>
    <input type="checkbox" 
           name="ch3sapi_options[<?php echo $name;  ?>]" 
           <?php if ( isset( $options[$name] ) && $options[$name] ) echo ' checked="checked"';?>/>
<?php }

// function to add menu under setting main menu
add_action( 'admin_menu', 'ch3sapi_settings_menu' );
function ch3sapi_settings_menu() {
    add_options_page( 
            'My Google Analytics Configuration', //$page_title
            'My Google Analytics - Settings API', //$menu_title
            'manage_options', //$capability
            'ch3sapi-my-google-analytics', //$menu_slug
            'ch3sapi_config_page' ); //$function
}
function ch3sapi_config_page() { ?>
    <div id="ch3sapi-general" class="wrap">
        <h2>My Google Analytics – Settings API</h2>
        <form name="ch3sapi_options_form_settings_api" method="post" action="options.php">
            <?php //settings_fields($option_group); ?>
            <?php // $option_group of register setting func
            settings_fields( 'ch3sapi_settings' ); ?>
            <?php //do_settings_sections($page); ?>
            <?php // $page of add_settings_section func
            do_settings_sections( 'ch3sapi_settings_section' ); ?> 
            <input type="submit" value="Submit" class="button-primary" />
        </form>
    </div>
<?php }