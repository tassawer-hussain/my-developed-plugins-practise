<?php

/* 
Plugin Name: Ch2.4 Email Link
Plugin URI: tassawer.apphb.com 
Description: Append an email link at the end of each content
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
 */

add_filter( 'the_content', 'ch2epl_email_page_filter' );
function ch2epl_email_page_filter ( $the_content ) {
    // build url to mail message icon downloaded from iconarchive.com
    $mail_icon_url = plugins_url( 'Mail-icon.png', __FILE__ );
    
    // Set initial value of $new_content variable to previous content
    $new_content = $the_content;
    // Append image with mailto link after content, including
    // the item title and permanent URL
    $new_content .= "<a title='E-mail article link'  
        href='mailto:someone@somewhere.com?subject=Check out this interesting article entitled ";
    $new_content .= get_the_title();
    $new_content .= "&body=Hi!%0A%0AI thought you would enjoy this article entitled ";
    $new_content .= get_the_title();
    $new_content .= ".%0A%0A";
    $new_content .= get_permalink();
    $new_content .= "%0A%0AEnjoy!'> 
        <img alt='' src='" . $mail_icon_url. "' /></a>";
   
    // Return filtered content for display on the site
    return $new_content;
}