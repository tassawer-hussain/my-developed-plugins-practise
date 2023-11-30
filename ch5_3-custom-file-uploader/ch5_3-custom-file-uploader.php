<?php

/* 
Plugin Name: Ch5.3 Custom file uploader
Plugin URI: tassawer.apphb.com 
Description: How to upload a custom file from post and pages editors
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
 */

// executed when WordPress is rendering the HTML
// code at the beginning of the post editor form
add_action( 'post_edit_form_tag', 'ch5_cfu_form_add_enctype' );
function ch5_cfu_form_add_enctype() { 
    echo ' enctype="multipart/form-data"'; 
}

// called when WordPress is preparing the meta boxes for
// all administration sections
add_action( 'add_meta_boxes', 'ch5_cfu_register_meta_box' );
function ch5_cfu_register_meta_box() {
    add_meta_box(
            'ch5_cfu_upload_file', // $id
            'Upload File', // $title
            'ch5_cfu_upload_meta_box', // $callback
            'post', // $screen
            'normal' ); // $context 
    add_meta_box(
            'ch5_cfu_upload_file', // $id
            'Upload File', // $title
            'ch5_cfu_upload_meta_box', // $callback
            'page', // $screen
            'normal' ); // $context
}
function ch5_cfu_upload_meta_box( $post ) { ?>
    <table>
        <tr>
            <td style="width: 150px">PDF Attachment</td>
            <td>
            <?php
                // Retrieve attachment data for post
                $attachment_data = get_post_meta( $post->ID, 'attachdata', true );
               
                // Display post link if data is present
                if ( empty ( $attachment_data ) ) {
                    echo 'No Attachment Present';
                } else {
                    echo '<a href="';
                    echo esc_url( $attachment_data['url'] );
                    echo '">' . 'Download Attachment</a>';
                }               
            ?>
            </td>
        </tr>
        <tr>
            <td>Upload File</td>
            <td><input name="uploadpdf" type="file" /></td>
        </tr>
        <tr>
            <td>Delete File</td>
            <td><input type="submit" name="deleteattachment"  
                class="button-primary" id="deleteattachment" 
                value="Delete Attachment" /></td>
        </tr>
    </table>
<?php }

// executed when post data is processed to be saved:
add_action( 'save_post', 'ch5_cfu_save_uploaded_file', 10, 2 );
function ch5_cfu_save_uploaded_file( $post_id = false, $post = false ) {
    if ( isset($_POST['deleteattachment'] ) ) {
        $attach_data = get_post_meta( $post_id, "attachdata", true );
        if ( $attach_data != "" ) {
            unlink( $attach_data['file'] );
            delete_post_meta( $post_id, 'attachdata' );
        }
    } elseif ( $post->post_type == 'post' || $post->post_type == 'page' ) {
        // Look to see if file has been uploaded by user
        if( array_key_exists( 'uploadpdf', $_FILES ) && !$_FILES['uploadpdf']['error'] ) {
            // Retrieve file type and store lower-case version
            $file_type_array = wp_check_filetype( basename($_FILES['uploadpdf']['name']) );
            $pdf_file_type = strtolower( $file_type_array['ext']  
            );
            // Display error message if file is not a PDF
            if ( $pdf_file_type != 'pdf' ) {
                wp_die( 'Only files of PDF type are allowed.' );
                exit;
            } else {
                // Send uploaded file data to upload directory
                $upload_return = wp_upload_bits(
                        $_FILES['uploadpdf']['name'],
                        null,
                        file_get_contents($_FILES['uploadpdf']['tmp_name']) );
                // Replace backslashes with slashes
                $upload_return['file'] = str_replace( '\\', '/', $upload_return['file'] );
                
                // Set upload path data if successful.
                if ( isset($upload_return['error']) && $upload_return['error'] != 0 ) {
                    $errormsg = 'There was an error uploading';
                    $errormsg .= 'your file. The error is: ';
                    $errormsg .= $upload_return['error'];
                    wp_die( $errormsg ); 
                    exit;
                } else {
                    $attach_data = get_post_meta( $post_id, 'attachdata', true );
                    if ( $attach_data != '' )
                      unlink( $attach_data['file'] );
                    update_post_meta( $post_id, 'attachdata', $upload_return );
                }
            }
        }
    }
}

// function to display uploaded file in theme files
function ch5_cfu_display_pdf_link( $pdf_link_text = '', $before_link = '', $after_link = '', $post_id = '') {
    $post_id = ( !empty($post_id) ? $post_id : get_the_ID() );
    $pdf_link_text = ( !empty($pdf_link_text) ? $pdf_link_text : 'PDF Attachment' );
    $before_link = ( !empty($before_link) ? $before_link : '<div class="PDFAttach">' );
    $after_link = ( !empty($after_link) ? $after_link : '</div>');
    $attachment_data = get_post_meta($post_id, 'attachdata', true );
    if ( empty( $attachment_data ) ) {
        echo 'No PDF Attachment Present';
    } else {
        echo $before_link . '<a href="';
        echo esc_html( $attachment_data['url'] );
        echo '">' . $pdf_link_text;
        echo '</a>' . $after_link;
    }    
}