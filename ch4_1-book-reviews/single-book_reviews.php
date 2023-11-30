<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

get_header(); ?>
<div id="primary">
    <div id="content" role="main"> 
        <!-- Cycle through all posts -->
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>           
                <header class="entry-header">
                    <!-- Display featured image in right-aligned floating div -->
                    <div style="float: right; margin: 10px">
                        <?php the_post_thumbnail( 'large' ); ?>
                    </div>
                    <!-- Display Title and Author Name -->
                    <strong>Title: </strong><?php the_title(); ?><br />
                    <strong>Author: </strong>
                    <?php echo esc_html( get_post_meta( get_the_ID(), 'book_author', true ) ); ?>
                     <br />
                    <strong>Type: </strong>
                    <?php
                        $book_types = wp_get_post_terms( get_the_ID(), 'book_reviews_book_type' );
                        if ( $book_types ) {
                            $first_entry = true;
                            for ( $i = 0; $i < count( $book_types ); $i++ ) {
                                if ( $first_entry == false )
                                    echo ', ';
                                echo $book_types[$i]->name;
                                $first_entry = false;
                            }
                        }
                        else
                            echo 'None Assigned';
                    ?>
                    <br /> 
                     
                    <!-- Display yellow stars based on rating -->
                    <strong>Rating: </strong>
                    <?php  
                        $nb_stars = intval( get_post_meta( get_the_ID(), 'book_rating', true ) );
                        for ( $star_counter = 1; $star_counter <= 5; $star_counter++ ) {
                            if ( $star_counter <= $nb_stars ) {
                                echo '<img src="' . plugins_url( 'ch4_1-book-reviews/star-icon.png' ) . '" />';
                            } else {
                                echo '<img src="' . plugins_url( 'ch4_1-book-reviews/star-icon-grey.png' ) . '" />';
                            }
                        } ?>
                </header>
                <!-- Display book review contents -->
                <div class="entry-content"><?php the_content(); ?></div>
            </article>
            <!-- Display comment form -->
            <?php comments_template( '', true ); ?>
        <?php endwhile; ?>
    </div>
</div>
<?php get_footer(); ?>