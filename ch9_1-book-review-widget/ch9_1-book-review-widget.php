<?php

/* 
Plugin Name: Ch9.1 Book review Widgets
Plugin URI: tassawer.apphb.com 
Description: Plugin to demonstrate how to create a widget in wordpress
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
 */

// called when widgets are initialized
add_action( 'widgets_init', 'ch9brw_create_widgets' );
function ch9brw_create_widgets() {
    register_widget( 'Book_Reviews' );
}

class Book_Reviews extends WP_Widget {
    // Construction function
    function __construct () {
        parent::__construct( 'book_reviews', 'Book Reviews',
                             array( 'description' => 'Displays list of recent book reviews' ) );
    }
    
    function form( $instance ) {
        // Retrieve previous values from instance or set default values if not present
        $render_widget = ( !empty( $instance['render_widget'] ) ? $instance['render_widget'] : 'true' );
        $nb_book_reviews = ( !empty( $instance['nb_book_reviews'] ) ? $instance['nb_book_reviews'] : 5 );
        $widget_title = ( !empty( $instance['widget_title'] ) ? esc_attr( $instance['widget_title'] ) : 'Book Reviews' );
        ?>

        <!-- Display fields to specify title and item count -->
        <p>
            <label for="<?php echo $this->get_field_id( 'render_widget' ); ?>">
                <?php echo 'Display Widget'; ?>           
                <select id="<?php echo $this->get_field_id( 'render_widget' ); ?>"
                    name="<?php echo $this->get_field_name( 'render_widget' ); ?>">
                    <option value="true" <?php selected( $render_widget, 'true' ); ?>>
                        Yes
                    </option>
                    <option value="false" <?php selected( $render_widget, 'false' ); ?>>
                        No
                    </option>
                </select>                   
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'widget_title' ); ?>">
                <?php echo 'Widget Title:'; ?>           
                <input type="text" id="<?php echo $this->get_field_id( 'widget_title' );?>"
                    name="<?php echo $this->get_field_name( 'widget_title' ); ?>"
                    value="<?php echo $widget_title; ?>" />           
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'nb_book_reviews' ); ?>">
                <?php echo 'Number of reviews to display:'; ?>           
                <input type="text" id="<?php echo $this->get_field_id( 'nb_book_reviews' ); ?>"
                    name="<?php echo $this->get_field_name( 'nb_book_reviews' ); ?>"
                    value="<?php echo $nb_book_reviews; ?>" />           
            </label>
        </p>
    <?php }
    
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        // Only allow numeric values
        if ( is_numeric ( $new_instance['nb_book_reviews'] ) )
            $instance['nb_book_reviews'] = intval( $new_instance['nb_book_reviews'] );
        else
            $instance['nb_book_reviews'] = $instance['nb_book_reviews'];

        $instance['widget_title'] = strip_tags( $new_instance['widget_title'] );
        $instance['render_widget'] = strip_tags( $new_instance['render_widget'] );     
        
        return $instance;
    }
    
    function widget( $args, $instance ) {
        if ( $instance['render_widget'] == 'true' ) {
            // Extract members of args array as individual variables
            extract( $args );
            
            // Retrieve widget configuration options
            $nb_book_reviews =  ( !empty( $instance['nb_book_reviews'] ) ? $instance['nb_book_reviews'] : 5 );
            $widget_title = ( !empty( $instance['widget_title'] ) ? esc_attr( $instance['widget_title'] ) : 'Book Reviews' );

            // Preparation of query string to retrieve book reviews
            $query_array = array( 'post_type' => 'book_reviews',
                                  'post_status' => 'publish',
                                  'posts_per_page' => $nb_book_reviews );

            // Execution of post query
            $book_review_query = new WP_Query();
            $book_review_query->query( $query_array ); 

            // Display widget title
            echo $before_widget;       
            echo $before_title;
            echo apply_filters( 'widget_title', $widget_title );
            echo $after_title; 
            // Check if any posts were returned by query
            if ( $book_review_query->have_posts() ) {           
                // Display posts in unordered list layout
                echo '<ul>';
                // Cycle through all items retrieved
                while ( $book_review_query->have_posts() ) {
                    $book_review_query->the_post();
                    echo '<li><a href="' . get_permalink() . '">';
                    echo get_the_title( get_the_ID() ) . '</a></li>';
                }
                echo '</ul>';
            }
            wp_reset_query();
            echo $after_widget;
        }
    }    
}