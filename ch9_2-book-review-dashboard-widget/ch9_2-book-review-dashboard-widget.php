<?php

/* 
Plugin Name: Ch9.2 Book Review Dashboard Widgets
Plugin URI: tassawer.apphb.com 
Description: Create a widget on wordpress dashboard
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
 */

// called when the dashboard contents are being prepared
add_action( 'wp_dashboard_setup', 'ch9brdw_add_dashboard_widget' );
function ch9brdw_add_dashboard_widget() {
    wp_add_dashboard_widget( 'book_reviews_dashboard_widget', // $widget_id
                             'Book Reviews', // $widget_name
                             'ch9brdw_dashboard_widget' ); // $callback
}

function ch9brdw_dashboard_widget() {
    $book_review_count = wp_count_posts( 'book_reviews' ); ?>
    <a href="<?php echo add_query_arg( array(
                                  'post_status' => 'publish',
                                  'post_type' => 'book_reviews' ),
                                  admin_url( 'edit.php' ) ); ?>">
        <strong>
              <?php echo $book_review_count->publish; ?>
        </strong> Published
    </a>
    <br />
    <a href="<?php echo add_query_arg( array(
                                  'post_status' => 'draft',
                                  'post_type' => 'book_reviews' ),
                                  admin_url( 'edit.php' ) ); ?>">
        <strong>
            <?php echo $book_review_count->draft; ?>
        </strong> Draft
    </a>
<?php }