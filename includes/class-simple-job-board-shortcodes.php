<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Simple_Job_Board_Shortcodes class.
 *
 * @link        https://wordpress.org/plugins/simple-job-board
 * @since       1.0.0
 *
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/includes
 */

/**
 * This is used to define custom post types.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since       1.0.0
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/includes
 * @author      PressTigers <support@presstigers.com>
 */

class Simple_Job_Board_Shortcodes
{
   /**
     * Constructor
     */
    public function __construct()
    {
        add_shortcode( 'jobpost', array ( $this, 'jobs_listing' ) );
        add_shortcode( 'job', array( $this, 'output_job' ) );
    }
    
    /**
     * jobs_listing function.
     *
     * @access public
     * @param mixed $args
     * @return void
     */
    public function jobs_listing( $atts )
    {
        // Shortcode Default Array
        $a = shortcode_atts ( array (
                'posts'    => '-1',
                'excerpt'  => 'yes',
                'category' => '',
                'type'     => '',
                'location' => '',
                'keywords' => '',
                'order'    => 'DESC',
            ),
            $atts
        );
        
        // Job Search Filter Check
        if ( isset ( $_GET[ 'selected_category' ] ) || isset ( $_GET[ 'selected_jobtype' ] ) || isset ( $_GET[ 'selected_location' ] ) ) {
            $args = array (
                'posts_per_page' => $a[ 'posts' ],
                'post_type' => 'jobpost',
            );
            
            // Merge $arg array on each $_GET element
            if ( isset ( $_GET[ 'selected_category' ] ) && -1 != $_GET[ 'selected_category' ] )
                $args = array_merge ( $args, array ( 'jobpost_category' => $_GET[ 'selected_category' ] ) );
            if ( isset ( $_GET[ 'selected_jobtype' ] ) && -1 != $_GET[ 'selected_jobtype' ] )
                $args = array_merge ( $args, array ( 'jobpost_job_type' => $_GET[ 'selected_jobtype' ] ) );
            if ( isset ( $_GET[ 'selected_location' ] ) && -1 != $_GET[ 'selected_location' ] )
                $args = array_merge ( $args, array ( 'jobpost_location' => $_GET[ 'selected_location' ] ) );
            if ( !empty ( $_GET[ 'search_keywords' ] ) ) {                              
                $args = array_merge ( $args, array ( 's' => sanitize_text_field ( $_GET[ 'search_keywords' ] ) ) );
            }
            
        } else {
            $args = array (
                'posts_per_page'   => $a[ 'posts' ],
                'post_type'        => 'jobpost',
                'jobpost_category' => $a[ 'category' ],
                'jobpost_job_type' => $a[ 'type' ],
                'jobpost_location' => $a[ 'location' ],
            );
        }
        $search_result = query_posts ( $args );
        
        get_simple_job_board_template( 'job-filters.php', array( 'per_page' => $a[ 'posts' ], 'order' => $a[ 'order' ], 'categories' => $a[ 'category' ], 'job_types' => $a[ 'type' ], 'atts' => $atts, 'location' => $a[ 'location' ], 'keywords' => $a[ 'keywords' ] ) );
        
        if ( have_posts () ):
            get_simple_job_board_template ( 'job-listings-start.php' );
            while ( have_posts () ): the_post ();
                get_simple_job_board_template_part ( 'content', 'job-listing' );
            endwhile;
            get_simple_job_board_template ( 'job-listings-end.php' );
        else:
            get_simple_job_board_template ( 'content-no-jobs-found.php' );
        endif;
        $html = ob_get_clean ();
        wp_reset_query ();
        return $html;
    }
    
    /**
    * output_job function.
    *
    * @access public
    * @param array $args
    * @return string
    */
    public function output_job( $atts )
    {
        extract( shortcode_atts( array( 
            'id' => '',
            ), $atts ) );

        if ( ! $id )
            return;

        ob_start();
        $args = array(
            'post_type'   => 'jobpost',
            'post_status' => 'publish',
            'p'           => $id
        );
        $jobs = new WP_Query( $args );
        if ( $jobs->have_posts() ) : ?>
            <?php while ( $jobs->have_posts() ) : $jobs->the_post(); ?>
                <h1><?php the_title(); ?></h1>
                <?php get_simple_job_board_template_part( 'content-single', 'job-listing' ); ?>
            <?php endwhile; ?>
        <?php endif;
        wp_reset_postdata();
        return '<div class="job-shortcode single-job-listing">' . ob_get_clean() . '</div>';
    }
}
new Simple_Job_Board_Shortcodes();