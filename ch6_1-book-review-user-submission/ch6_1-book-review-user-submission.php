<?php

/* 
Plugin Name: Ch6.1 Book Review User Submission
Plugin URI: tassawer.apphb.com 
Description: Plugin to handle user book review submission form
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
 */

add_shortcode( 'submit-book-review', 'ch6_brus_book_review_form' );
function ch6_brus_book_review_form() {
    // make sure user is logged in
    if ( !is_user_logged_in() ) {
        echo '<p>You need to be a site member to be able to ';
        echo 'submit book reviews. Sign up to gain access!</p>';
        return;
    } ?>
    <form method="post" id="addbookreview" action="">
    <!-- Nonce fields to verify visitor provenance -->
    <?php //wp_nonce_field($action, $name); ?>
    <?php wp_nonce_field( 'add_review_form', 'br_user_form' ); ?>

    <?php if ( isset( $_GET['addreviewmessage'] ) && $_GET['addreviewmessage'] == 1 ) { ?>
    <div style="margin: 8px; border: 1px solid #ddd; background-color: #ff0;">
        Thank for your submission!
    </div>
    <?php } ?>

    <!-- Post variable to indicate user-submitted items -->
    <input type="hidden" name="ch6_brus_user_book_review" value="1" />

    <table>
        <tr>
            <td>Book Title</td>
            <td><input type="text" name="book_title" /></td>
        </tr>
        <tr>
            <td>Book Author</td>
            <td><input type="text" name="book_author" /></td>
        </tr>
        <tr>
            <td>Review</td>
            <td><textarea name="book_review_text"></textarea></td>
        </tr>
        <tr>
          <td>Rating</td>
            <td><select name="book_review_rating">
            <?php
            // Generate all rating items in drop-down list
            for ( $rating = 5; $rating >= 1; $rating-- ) { ?>
                <option value="<?php echo $rating; ?>">
                               <?php echo $rating; ?> stars
                </option>
            <?php } ?>
            </select>
            </td>
        </tr>
        <tr>
            <td>Book Type</td>
            <td>
            <?php
            // Retrieve array of all book types in system
            $book_types = get_terms( 'book_reviews_book_type',
                                 array( 'orderby' => 'name',
                                        'hide_empty' => 0 ) );
            // Check if book types were found
            if ( !is_wp_error( $book_types ) && !empty( $book_types ) ) {
                echo '<select name="book_review_book_type">';
                // Display all book types
                foreach ( $book_types as $book_type ) {
                     echo '<option value="' . $book_type->term_id;
                     echo '">' . $book_type->name . '</option>';
               }   
                echo '</select>';
            } ?>
            </td>
        </tr>
        <tr>
            <td>Re-type the following text<br />
                <img src="<?php echo plugins_url('EasyCaptcha/easycaptcha.php', __FILE__); ?>" />
            </td>
            <td><input type="text" name="book_review_captcha" /></td>
        </tr>
    </table>
    <input type="submit" name="submit" value="Submit Review" />
    </form>
<?php }
    
// function that will intercept user-submitted book reviews
add_action( 'template_redirect', 'ch6_brus_match_new_book_reviews' );
function ch6_brus_match_new_book_reviews( $template ) {
    if ( !empty( $_POST['ch6_brus_user_book_review'] ) ) {
        ch6_brus_process_user_book_reviews();
    } else {
        return $template;
    }
}
function ch6_brus_process_user_book_reviews() {
    // Check that all required fields are present and non-empty
    if ( wp_verify_nonce( $_POST['br_user_form'], 'add_review_form' ) &&
         !empty( $_POST['book_title'] ) &&
         !empty( $_POST['book_author'] ) &&
         !empty( $_POST['book_review_text'] ) &&
         !empty( $_POST['book_review_book_type'] ) &&
         !empty( $_POST['book_review_rating'] ) &&
         !empty($_POST['book_review_captcha'] ) ) {
        
        // Variable used to determine if submission is valid
        $valid = false;
   
        // Check if captcha text was entered
        if ( empty( $_POST['book_review_captcha'] ) ) {
            $abortmessage = 'Captcha code is missing. Go back and ';
            $abortmessage .= 'provide the code.';
            wp_die( $abortmessage );
            exit;
        } else {
            // Check if captcha cookie is set
            if ( isset( $_COOKIE['Captcha'] ) ) {
                list( $hash, $time ) = explode( '.', $_COOKIE['Captcha'] );
            
                // The code under the md5's first section needs to match
                // the code entered in easycaptcha.php
               if ( md5('ILIKEYOUMOSTPAGLII'. 
                    $_REQUEST['book_review_captcha'] . 
                    $_SERVER['REMOTE_ADDR'] . $time ) != $hash ) {
                    $abortmessage = ' Captcha code is wrong. Go back ';
                    $abortmessage .= 'and try to get it right or reload ';
                    $abortmessage .= 'to get a new captcha code.';
                    wp_die( $abortmessage );
                    exit;
                } elseif( ( time() - 5 * 60) > $time ) {
                    $abortmessage = 'Captcha timed out. Please go back, ';
                    $abortmessage .= 'reload the page and submit again.';
                    wp_die( $abortmessage );
                    exit;
                } else {
                    // Set flag to accept and store user input
                    $valid = true;
                }
            } else {
                $abortmessage = 'No captcha cookie given. Make sure ';
                $abortmessage .= 'cookies are enabled.';
                wp_die( $abortmessage );
                exit;
            }
        }
        
        if($valid) {
            // Create array with received data
            $new_book_review_data = array(
                'post_status' => 'draft',
                'post_title' => $_POST['book_title'],
                'post_type' => 'book_reviews',
                'post_content' => $_POST['book_review_text'] );

            // Insert new post in site database
            // Store new post ID from return value in variable
            $new_book_review_id = wp_insert_post( $new_book_review_data );

            // Store book author and rating
            add_post_meta( $new_book_review_id, 'book_author', wp_kses($_POST['book_author'], array() ) );
            add_post_meta( $new_book_review_id, 'book_rating', (int)$_POST['book_review_rating'] );

            // Set book type on post
            if ( term_exists( $_POST['book_review_book_type'], 'book_reviews_book_type' ) ) {
                wp_set_post_terms( $new_book_review_id, $_POST['book_review_book_type'], 'book_reviews_book_type' );
            }

            // Redirect browser to book review submission page
            $redirectaddress =( empty($_POST['_wp_http_referer']) ? site_url() : $_POST['_wp_http_referer'] );
            wp_redirect( add_query_arg( 'addreviewmessage', '1',
                         $redirectaddress ) );
            exit;
        }
    } else {
        // Display message if any required fields are missing
        $abortmessage = 'Some fields were left empty. Please ';
        $abortmessage .= 'go back and complete the form.';
        wp_die($abortmessage);
        exit;
    }
}

// called back when new posts are submitted
add_action( 'wp_insert_post', 'ch6_brus_send_email', 10, 2 );
function ch6_brus_send_email( $post_id, $post ) {
    // Only send e-mails for user-submitted book reviews
    if ( isset( $_POST['ch6_brus_user_book_review'] ) && 'book_reviews' == $post->post_type ) {
        $admin_mail = get_option('admin_email');
        $headers = 'Content-type: text/html';
        $message = 'A user submitted a new book review to your ';
        $message .= 'Wordpress site database.<br /><br />';
        $message .= 'Book Title: ' . $post->post_title ;
        $message .= '<br />';
        $message .= '<a href="';
        $message .= add_query_arg( array(  
                                  'post_status' => 'draft',
                                  'post_type' => 'book_reviews' ),
                                   admin_url( 'edit.php' ) );
        $message .= '">Moderate new book reviews</a>';
        $email_title = htmlspecialchars_decode( get_bloginfo(),  
            ENT_QUOTES ) . " - New Book Review Added: " . 
            htmlspecialchars( $_POST['book_title'] );
        // Send e-mail
        wp_mail( $admin_mail, $email_title, $message, $headers );
    }
}