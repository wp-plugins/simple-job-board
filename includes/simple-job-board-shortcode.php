<?php

/**
 * Shortcode Generator
 * @param type $atts
 * @return type
 */
function job_board_shortcode ( $atts )
{

    // Job Filters 
    job_filter_form ();

    // Shortcode Default Array
    $a = shortcode_atts (
            array (
        'posts' => '-1',
        'excerpt' => 'yes',
        'category' => '',
        'type' => '',
        'location' => '',
            ), $atts
    );

    // Job Search Filter Check
    if ( isset ( $_POST[ 'category_list' ] ) || isset ( $_POST[ 'jobtype_list' ] ) || isset ( $_POST[ 'job_location_list' ] ) ) {
        $args = array (
            'posts_per_page' => $a[ 'posts' ],
            'post_type' => 'jobpost',
        );

        // Merge $arg array on each $_POST element
        if ( isset ( $_POST[ 'category_list' ] ) && -1 != $_POST[ 'category_list' ] )
            $args = array_merge ( $args, array ( 'jobpost_category' => $_POST[ 'category_list' ] ) );
        if ( isset ( $_POST[ 'jobtype_list' ] ) && -1 != $_POST[ 'jobtype_list' ] )
            $args = array_merge ( $args, array ( 'jobpost_job_type' => $_POST[ 'jobtype_list' ] ) );
        if ( isset ( $_POST[ 'job_location_list' ] ) && -1 != $_POST[ 'job_location_list' ] )
            $args = array_merge ( $args, array ( 'jobpost_location' => $_POST[ 'job_location_list' ] ) );
    } else {
        $args = array (
            'posts_per_page' => $a[ 'posts' ],
            'post_type' => 'jobpost',
            'jobpost_category' => $a[ 'category' ],
            'jobpost_job_type' => $a[ 'type' ],
            'jobpost_location' => $a[ 'location' ],
        );
    }
    $search_result = query_posts ( $args );
    global $wp_query;
    
    if ( 1 > $wp_query->found_posts ) {
        echo "No Job Found";
    }
    
    ob_start ();
    if ( have_posts () ): while ( have_posts () ): the_post ();
            global $post;
            $job_category = wp_get_post_terms ( $post->ID, 'jobpost_category' );
            $job_type = wp_get_post_terms ( $post->ID, 'jobpost_job_type' );
            $job_location = wp_get_post_terms ( $post->ID, 'jobpost_location' );
            ?>            
            <article class="job-listing">
                <div class="loop-item-wrap">
                    <div class="loop-item-content">
                        <h2 class="job-title">
                            <a href="<?php echo get_the_permalink (); ?>" title="<?php the_title (); ?>"><?php the_title (); ?></a>
                        </h2>
                        <p class="job-meta">
                            <?php if ( NULL != $job_category ):foreach ( $job_category as $cat ) { ?>
                                    <span> <?php echo $cat->name; ?></span> <?php
                                }
                            endif;
                            ?>
                            <?php if ( NULL != $job_type ):foreach ( $job_type as $type ) { ?>

                                    <span class="job-type"><i class="fa fa-bookmark"></i> <?php echo $type->name; ?></span> 
                                    <?php
                                }
                            endif;
                            ?>
                            <?php if ( NULL != $job_location ):foreach ( $job_location as $location ) { ?>

                                    <span class="job-type"><i class="fa fa-location-arrow"></i> <?php echo $location->name; ?></span> 
                                    <?php
                                }
                            endif;
                            ?>
                            <span>
                                <time class="entry-date" datetime="2015-01-23T09:22:47+00:00">
                                    <i class="fa fa-calendar"></i>
                                    <?php echo get_the_date (); ?>
                                </time>
                            </span>
                        </p>
                        <a class="btn btn-primary view-detail-job" href="<?php echo get_the_permalink (); ?>"><input type="submit" value="View Details"/></a></div>
                </div>
            </article>
            <hr />
            <?php
        endwhile;
    endif;
    $html = ob_get_clean ();
    wp_reset_query ();
    return $html;
}

// Register Shortcode
add_shortcode ( 'jobpost', 'job_board_shortcode' );
