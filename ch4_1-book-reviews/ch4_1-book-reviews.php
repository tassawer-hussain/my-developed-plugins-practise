<?php

/* 
Plugin Name: Ch4.1 Book Review - Custom Post Type 
Description: Create a custom post type
Version: 1.0
Author: Tassawer Hussain
Author URI: tassawer.apphb.com
License: GPLv2
 */

add_action( 'init', 'ch4_br_create_book_post_type' );
function ch4_br_create_book_post_type() {
    register_post_type( 'book_reviews',
            array(
                'labels' => array(
                    'name' => 'Book Reviews',
                    'singular_name' => 'Book Review',
                    'add_new' => 'Add New',
                    'add_new_item' => 'Add New Book Review',
                    'edit' => 'Edit',
                    'edit_item' => 'Edit Book Review',
                    'new_item' => 'New Book Review',
                    'view' => 'View',
                    'view_item' => 'View Book Review',
                    'search_items' => 'Search Book Reviews',
                    'not_found' => 'No Book Reviews found',
                    'not_found_in_trash' => 'No Book Reviews found in Trash',
                    'parent' => 'Parent Book Review'                   
                    ),
                'public' => true,
                'menu_position' => 20,
                'supports' => array( 
                    'title', 'editor', 'comments','thumbnail'),
                'taxonomies' => array( '' ),
                'menu_icon' => plugins_url( 'Books-icon.png', __FILE__ ),
                'has_archive' => true
                //'rewrite' => array('slug' => 'awesome_book_reviews')
            )
    );
    
    register_taxonomy(
            'book_reviews_book_type', // $taxonomy_name. a unique identifier
            'book_reviews', // $post_type
            array( // $args
                'labels' => array(
                    'name' => 'Book Type',
                    'add_new_item' => 'Add New Book Type',
                    'new_item_name' => "New Book Type Name"
                    ),
                'show_ui' => false, // set it false to hide taxonomy ui in backend
                'show_tagcloud' => false,
                'hierarchical' => true
                )
            );
}

// called when the administration interface is visited:
add_action( 'admin_init', 'ch4_br_admin_init' );
function ch4_br_admin_init() {
    add_meta_box( 
            'ch4_br_review_details_meta_box', // $id
            'Book Review Details', // $title
            'ch4_br_display_review_details_meta_box', // $callback
            'book_reviews', // $screen
            'normal', // $context
            'high' ); // $priority
}
function ch4_br_display_review_details_meta_box( $book_review ) {
    // Retrieve current author and rating based on review ID
    $book_author = esc_html( get_post_meta( $book_review->ID, 'book_author', true ) );
    $book_rating = intval( get_post_meta( $book_review->ID, 'book_rating', true ) ); ?>
    <table>
        <tr>
            <td style="width: 100%">Book Author</td>
            <td><input type="text" size="80"  
                name="book_review_author_name" 
                value="<?php echo $book_author; ?>" /></td>
        </tr>
        <tr>
            <td style="width: 150px">Book Rating</td>
             <td>
                 <select style="width: 100px" name="book_review_rating">
                 <?php
                 // Generate all items of drop-down list
                 for ( $rating = 5; $rating >= 1; $rating -- ) { ?>
                      <option value="<?php echo $rating; ?>"
                         <?php echo selected( $rating, $book_rating ); ?>>
                         <?php echo $rating; ?> stars
                      </option>
                 <?php } ?>
                 </select>
            </td>
        </tr>
        <tr>
            <td>Book Type</td>
            <td><?php
            // Retrieve array of types assigned to post
            $assigned_types = wp_get_post_terms( $book_review->ID, 'book_reviews_book_type' );

            // Retrieve array of all book types in system
            $book_types = get_terms( 'book_reviews_book_type', array( 
                'orderby' => 'name',
                'hide_empty' => 0) );
            
            if ( $book_types ) {
                echo '<select name="book_review_book_type"';
                echo ' style="width: 400px">';
                foreach ( $book_types as $book_type ) {               
                    echo '<option value="' . $book_type->term_id;
                    echo '" ' . selected( $assigned_types[0]->term_id, $book_type->term_id ) . '>';
                    echo esc_html( $book_type->name );
                    echo '</option>';
                 }        
                 echo '</select>';
            } ?>
            </td>
        </tr>
    </table>
<?php }

add_action( 'save_post', 'ch4_br_add_book_review_fields', 10, 2 );
function ch4_br_add_book_review_fields( $book_review_id, $book_review ) {
    // Check post type for book reviews
    if ( $book_review->post_type == 'book_reviews' ) {
        // Store data in post meta table if present in post data
        if ( isset( $_POST['book_review_author_name'] ) && $_POST['book_review_author_name'] != '' ) {
            update_post_meta( $book_review_id, 'book_author', $_POST['book_review_author_name'] );
        }
       
        if ( isset( $_POST['book_review_rating'] ) && $_POST['book_review_rating'] != '' ) {
            update_post_meta( $book_review_id, 'book_rating', $_POST['book_review_rating'] );
        }
        
        if ( isset( $_POST['book_review_book_type'] ) && $_POST['book_review_book_type'] != '' ) {
            wp_set_post_terms( $book_review->ID, $_POST['book_review_book_type'], 'book_reviews_book_type' );
        }
    }
}

// called when the administration interface is visited
add_filter( 'template_include', 'ch4_br_template_include', 1 );
function ch4_br_template_include( $template_path ) {   
    if ( get_post_type() == 'book_reviews' ) {
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array( 'single-book_reviews.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) .'/single-book_reviews.php';
            }
        } elseif ( is_archive() ) {
            if ( $theme_file = locate_template( array( 'archive-book_reviews.php'))) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/archive-book_reviews.php';
            }
        }
    }
    return $template_path;
}

add_shortcode( 'book-review-list', 'ch4_br_book_review_list' );
function ch4_br_book_review_list() {
    // Preparation of query array to retrieve 5 book reviews
    $query_params = array( 
        'post_type' => 'book_reviews',
        'post_status' => 'publish',
        'posts_per_page' => 5 ); 
    
    // Retrieve page query variable, if present
    $page_num = ( get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1 );

    // If page number is higher than 1, add to query array
    if ( $page_num != 1 )
        $query_params['paged'] = $page_num;

    // Execution of post query
    $book_review_query = new WP_Query;
    $book_review_query->query( $query_params );
   
    // Check if any posts were returned by the query
    if ( $book_review_query->have_posts() ) {
        
        // Display posts in table layout
        $output = '<table>';
    
        $output .= '<tr><th style="width: 350px"><strong>Title</strong></th>';
        $output .= '<th><strong>Author</strong></th></tr>';
    
        // Cycle through all items retrieved
        while ( $book_review_query->have_posts() ) {
            $book_review_query->the_post();
    
            $output .= '<tr><td><a href="' . get_the_permalink();
            $output .= '">';
            $output .= get_the_title( get_the_ID() ) . '</a></td>';
            $output .= '<td>';
            $output .= esc_html( get_post_meta( get_the_ID(), 'book_author', true ) );
            $output .= '</td></tr>';
        }
    
        $output .= '</table>';

        // Display page navigation links
        if ( $book_review_query->max_num_pages > 1 ) {
            $output .= '<nav id="nav-below">';
            $output .= '<div class="nav-previous">';
            $output .= get_next_posts_link( '<span class="meta-nav">&larr;</span> Older reviews', $book_review_query->max_num_pages );
            $output .= '</div>';
            $output .= '<div class="nav-next">';
            $output .= get_previous_posts_link( 'Newer reviews <span class="meta-nav">&rarr;</span>', $book_review_query->max_num_pages );
            $output .= '</div>';
            $output .= '</nav>';
        }
    
        // Reset post data query
        wp_reset_postdata();
    }
    
    return $output;
}

// code to provide book type post category metabox
add_action( 'admin_menu', 'ch4_br_add_book_type_item' );
function ch4_br_add_book_type_item() {
    global $submenu;
    $submenu['edit.php?post_type=book_reviews'][501] = array( 
        'Book Type',
        'manage_options',
        admin_url( '/edit-tags.php?taxonomy=book_reviews_book_type&post_type=book_reviews' ) );
}

// called when the Book Review listings page is being prepared
add_filter( 'manage_edit-book_reviews_columns', 'ch4_br_add_columns' );
function ch4_br_add_columns( $columns ) {
    $columns['book_reviews_author'] = 'Author';
    $columns['book_reviews_rating'] = 'Rating';
    $columns['book_reviews_type'] = 'Type';
    unset( $columns['comments'] );
    return $columns;
}

// called when columns data is getting retrieved for each row in the post listing
add_action( 'manage_posts_custom_column', 'ch4_br_populate_columns' );
function ch4_br_populate_columns( $column ) {
    if ( 'book_reviews_author' == $column ) {
        $book_author = esc_html( get_post_meta( get_the_ID(), 'book_author', true ) );
        echo $book_author;
    } elseif ( 'book_reviews_rating' == $column ) {
        $book_rating = get_post_meta( get_the_ID(), 'book_rating', true );
        echo $book_rating . ' stars';
    } elseif ( 'book_reviews_type' == $column ) {       
        $book_types = wp_get_post_terms( get_the_ID(), 'book_reviews_book_type' );
               
        if ( $book_types ){
            $num = count($book_types);
            for($i=0; $i<$num; $i++) {
                if($i>0)
                    echo ', ';
                echo $book_types[$i]->name;
            }
        }
        else
            echo 'None Assigned';
    }
}

// called when WordPress identifies columns that will be sortable
// for the Book Reviews custom post type
add_filter( 'manage_edit-book_reviews_sortable_columns', 'ch4_br_author_column_sortable' );
function ch4_br_author_column_sortable( $columns ) {
    $columns['book_reviews_author'] = 'book_reviews_author';
    $columns['book_reviews_rating'] = 'book_reviews_rating';
    return $columns;
}

// called when data is requested to display post lists
add_filter( 'request', 'ch4_br_column_ordering' );
function ch4_br_column_ordering( $vars ) {
    if ( !is_admin() )
        return $vars;

    if ( isset( $vars['orderby'] ) && 'book_reviews_author' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
                                   'meta_key' => 'book_author',
                                   'orderby' => 'meta_value' ) );
    } elseif ( isset( $vars['orderby'] ) && 'book_reviews_rating' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
                               'meta_key' => 'book_rating',
                               'orderby' => 'meta_value_num' ) );
    }
    return $vars;
}

// called when WordPress is preparing the filter drop-down
// boxes for the post listings
add_action( 'restrict_manage_posts', 'ch4_br_book_type_filter_list' );
function ch4_br_book_type_filter_list() {
    $screen = get_current_screen();
    global $wp_query;
    if ( $screen->post_type == 'book_reviews' ) {
        wp_dropdown_categories(array(
            'show_option_all' =>  'Show All Book Types',
            'taxonomy'        =>  'book_reviews_book_type',
            'name'            =>  'book_reviews_book_type',
            'orderby'         =>  'name',
            'selected'        =>  ( isset( $wp_query->query['book_reviews_book_type'] ) ? $wp_query->query['book_reviews_book_type'] : '' ),
            'hierarchical'    =>  false,
            'depth'           =>  3,
            'show_count'      =>  false,
            'hide_empty'      =>  false,
        ));
    }
}

// called when the post display query is being prepared
add_filter( 'parse_query', 'ch4_br_perform_book_type_filtering' );
function ch4_br_perform_book_type_filtering( $query ) {
    $qv = &$query->query_vars;
    // errata in book empty() => !empty()
    if ( !empty($qv['book_reviews_book_type']) && is_numeric($qv['book_reviews_book_type'])) {
        $term = get_term_by( 'id', $qv['book_reviews_book_type'], 'book_reviews_book_type' );
        $qv['book_reviews_book_type'] = $term->slug;
    }
}
/*
add_filter( 'wp_title', 'ch4_br_format_book_review_title' );
function ch4_br_format_book_review_title( $the_title ) {
    if ( get_post_type() == 'book_reviews' && is_single() ) {
        $book_author = esc_html(get_post_meta(get_the_ID(), 'book_author', true ));
        $the_title .= ' by ' . $book_author;
    }
    return $the_title;
} */