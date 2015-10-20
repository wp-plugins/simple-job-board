<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Simple_Job_Board_Post_Types class
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
class Simple_Job_Board_Post_Types
{
    /**
     * Initialize the class and set its properties.
     *
     * @since   1.0.0
     */
    public function __construct()
    {
        add_action( 'init', array ( $this, 'create_post_types' ), 0 );
        
        add_filter( 'the_content', array( $this, 'job_content' ) );
        
        // Hook - Taxonomy -> Job Category ->  Add New Column
        add_filter( 'manage_edit-jobpost_category_columns', array ( $this, 'job_board_category_column' ) );

        // Hook - Taxonomy -> Job Category ->  Add Value to New Column
        add_filter( 'manage_jobpost_category_custom_column', array ( $this, 'job_board_category_column_value' ), 10, 3 );

        // Hook - Taxonomy -> Job Type ->  Add New Column
        add_filter( 'manage_edit-jobpost_job_type_columns', array ( $this, 'job_board_job_type_column' ) );

        // Hook - Taxonomy -> Job Type ->  Add Value to New Column
        add_filter( 'manage_jobpost_job_type_custom_column', array ( $this, 'job_board_job_type_column_value' ), 10, 3 );

        // Hook - Taxonomy -> Job Location ->  Add New Column
        add_filter( 'manage_edit-jobpost_location_columns', array ( $this, 'job_board_job_location_column' ) );

        // Hook - Taxonomy -> Job Location ->  Add Value to New Column
        add_filter( 'manage_jobpost_location_custom_column', array ( $this, 'job_board_job_location_column_value' ), 10, 3 );

        // Hook - Delete Uploads on Applicant Deletion
        add_action( 'before_delete_post', array ( $this, 'job_board_delete_uploads' ) );

        // Hook - Applicant Listing - Column Name
        add_filter( 'manage_edit-jobpost_applicants_columns', array ( $this, 'job_board_applicant_list_columns' ) );

        // Hook - Applicant Listing - Column Value
        add_action( 'manage_jobpost_applicants_posts_custom_column', array ( $this, 'job_board_applicant_list_columns_value' ), 10, 2 );
    
        add_filter( 'the_job_description', 'wptexturize'        );
        add_filter( 'the_job_description', 'convert_smilies'    );
        add_filter( 'the_job_description', 'convert_chars'      );
        add_filter( 'the_job_description', 'wpautop'            );
        add_filter( 'the_job_description', 'shortcode_unautop'  );
        add_filter( 'the_job_description', 'prepend_attachment' );

    }
        
    /**
     * register_post_types function.
     *
     * @access  public
     * @return  void
     */
    public function create_post_types()
    {
        if ( post_type_exists( "jobpost" ) )
            return;

        /**
         * Post Types
         * Post Type -> Jobs
         */
        $singular = __( 'Job', 'simple-job-board' );
        $plural = __( 'Jobs', 'simple-job-board' );

        $labels = array (
            'name' => $plural,
            'singular_name'      => $singular,
            'menu_name'          => __( 'Job Board', 'simple-job-board' ),
            'all_items'          => sprintf( __( 'All %s', 'simple-job-board' ), $plural ),
            'add_new'            => __( 'Add New', 'simple-job-board' ),
            'add_new_item'       => sprintf( __( 'Add %s', 'simple-job-board' ), $singular ),
            'edit_item'          => sprintf( __( 'Edit %s', 'simple-job-board' ), $singular ),
            'new_item'           => sprintf( __( 'New %s', 'simple-job-board' ), $singular ),
            'view_item'          => sprintf( __( 'View %s', 'simple-job-board' ), $singular ),
            'search_items'       => sprintf( __( 'Search %s', 'simple-job-board' ), $plural ),
            'not_found'          => sprintf( __( 'No %s found', 'simple-job-board' ), $plural ),
            'not_found_in_trash' => sprintf( __( 'No %s found in trash', 'simple-job-board' ), $plural ),
            'parent'             => sprintf( __( 'Parent %s', 'simple-job-board' ), $singular )
        );

        $rewrite = TRUE;

        register_post_type( "jobpost", apply_filters( "register_post_type_jobpost", array (
            'labels' => $labels,
            'description'         => sprintf( __( 'This is where you can create and manage %s.', 'simple-job-board' ), $plural ),
            'public'              => TRUE,
            'exclude_from_search' => FALSE,
            'publicly_queryable'  => TRUE,
            'show_ui'             => TRUE,
            'show_in_nav_menus'   => TRUE,
            'menu_icon'           => 'dashicons-clipboard',
            'capability_type'     => 'post',
            'hierarchical'        => FALSE,
            'rewrite'             => array ( 'slug' => 'jobs' ),
            'query_var'           => TRUE,
            'supports'            => array ( 'title', 'editor', 'publicize' ),
                        )
        ) );

        /**
         * Taxonomies
         * Taxonomy -> Job Category
         */
        $singular = __( 'Job Category', 'simple-job-board' );
        $plural = __( 'Job Categories', 'simple-job-board' );

        $labels = array (
            'name' => $plural,
            'singular_name'     => $singular,
            'menu_name'         => ucwords( $plural ),
            'all_items'         => sprintf( __( 'All %s', 'simple-job-board' ), $plural ),
            'edit_item'         => sprintf( __( 'Edit %s', 'simple-job-board' ), $singular ),
            'update_item'       => sprintf( __( 'Update %s', 'simple-job-board' ), $singular ),
            'add_new_item'      => sprintf( __( 'Add New %s', 'simple-job-board' ), $singular ),
            'new_item_name'     => sprintf( __( 'New %s Name', 'simple-job-board' ), $singular ),
            'parent_item'       => sprintf( __( 'Parent %s', 'simple-job-board' ), $singular ),
            'parent_item_colon' => sprintf( __( 'Parent %s:', 'simple-job-board' ), $singular ),
            'search_items'      => sprintf( __( 'Search %s', 'simple-job-board' ), $plural ),
        );

        register_taxonomy( "jobpost_category", apply_filters( 'register_taxonomy_jobpost_category_object_type', array ( 'jobpost' )
                ), apply_filters( 'register_taxonomy_jobpost_category_args', array (
            'hierarchical' => TRUE,
            'label' => $plural,
            'labels' => $labels,
            'public' => TRUE,
            'rewrite' => $rewrite,
                        )
                )
        );

        /**
         * Taxonomies
         * Taxonomy -> Job Type
         */
        $singular = __( 'Job Type', 'simple-job-board' );
        $plural = __( 'Job Types', 'simple-job-board' );

        $labels = array (
            'name' => $plural,
            'singular_name'     => $singular,
            'menu_name'         => ucwords( $plural ),
            'all_items'         => sprintf( __( 'All %s', 'simple-job-board' ), $plural ),
            'edit_item'         => sprintf( __( 'Edit %s', 'simple-job-board' ), $singular ),
            'update_item'       => sprintf( __( 'Update %s', 'simple-job-board' ), $singular ),
            'add_new_item'      => sprintf( __( 'Add New %s', 'simple-job-board' ), $singular ),
            'new_item_name'     => sprintf( __( 'New %s Name', 'simple-job-board' ), $singular ),
            'parent_item'       => sprintf( __( 'Parent %s', 'simple-job-board' ), $singular ),
            'parent_item_colon' => sprintf( __( 'Parent %s:', 'simple-job-board' ), $singular ),
            'search_items'      => sprintf( __( 'Search %s', 'simple-job-board' ), $plural ),
        );

        register_taxonomy( "jobpost_job_type", apply_filters( 'register_taxonomy_jobpost_job_type_object_type', array ( 'jobpost' )
                ), apply_filters( 'register_taxonomy_jobpost_job_type_args', array (
            'hierarchical' => TRUE,
            'label' => $plural,
            'labels' => $labels,
            'public' => TRUE,
            'rewrite' => $rewrite,
                        )
                )
        );

        /**
         * Taxonomies
         * Taxonomy -> Location
         */
        $singular = __( 'Job Location', 'simple-job-board' );
        $plural = __( 'Job Locations', 'simple-job-board' );

        $labels = array (
            'name' => $plural,
            'singular_name'     => $singular,
            'menu_name'         => ucwords( $plural ),
            'all_items'         => sprintf( __( 'All %s', 'simple-job-board' ), $plural ),
            'edit_item'         => sprintf( __( 'Edit %s', 'simple-job-board' ), $singular ),
            'update_item'       => sprintf( __( 'Update %s', 'simple-job-board' ), $singular ),
            'add_new_item'      => sprintf( __( 'Add New %s', 'simple-job-board' ), $singular ),
            'new_item_name'     => sprintf( __( 'New %s Name', 'simple-job-board' ), $singular ),
            'parent_item'       => sprintf( __( 'Parent %s', 'simple-job-board' ), $singular ),
            'parent_item_colon' => sprintf( __( 'Parent %s:', 'simple-job-board' ), $singular ),
            'search_items'      => sprintf( __( 'Search %s', 'simple-job-board' ), $plural ),
        );

        register_taxonomy( "jobpost_location", apply_filters( 'register_taxonomy_jobpost_location_object_type', array ( 'jobpost' )
                ), apply_filters( 'register_taxonomy_jobpost_location_args', array (
            'hierarchical' => TRUE,
            'label'        => $plural,
            'labels'       => $labels,
            'public'       => TRUE,
            'rewrite'      => $rewrite,
                        )
                )
        );

        /**
         * Post Types
         * Post Type -> Applicants
         */
        $plural = __( 'Applicants', 'simple-job-board' );

        $labels = array (
            'edit_item' => sprintf( __( 'Edit %s', 'simple-job-board' ), $plural ),
        );

        $labels = array (
            'edit_item' => sprintf( __( 'Edit %s', 'simple-job-board' ), $plural ),
        );

        $args = array (
            'label'               => $plural,
            'labels'              => $labels,
            'description'         => sprintf( __( 'List of %s with their resume.', 'simple-job-board' ), $plural ),
            'public'              => FALSE,
            'exclude_from_search' => FALSE,
            'publicly_queryable'  => TRUE,
            'show_ui'             => TRUE,
            'show_in_menu'        => 'edit.php?post_type=jobpost',
            'show_in_nav_menus'   => TRUE,
            'menu_icon'           => 'dashicons-clipboard',
            'capabilities'        => array (
                'create_posts' => FALSE,
            ),
            'map_meta_cap' => TRUE,
            'hierarchical' => FALSE,
            'supports'     => array ( 'editor' )
        );

        register_post_type( "jobpost_applicants", apply_filters( "register_post_type_jobpost", $args
        ) );
    }
    
    /**
     * Add extra content when showing job content
     */
    public function job_content( $content )
    {
        global $post;
        
        if ( ! is_singular( 'jobpost' ) || ! in_the_loop() ) {
            return $content;
        }
        
        remove_filter( 'the_content', array( $this, 'job_content' ) );
        
        if ( is_single () and 'jobpost' === $post->post_type ) {
            ob_start();
            do_action( 'job_content_start' );
            get_simple_job_board_template_part( 'content-single', 'job-listing' );
            do_action( 'job_content_end' );
            $content = ob_get_clean();
        }
        
        add_filter( 'the_content', array( $this, 'job_content' ) );

        return apply_filters( 'simple_job_board_single_job_content', $content, $post );
   } 
    
    /**
     * Taxonomy -> Job Category ->  Add New Column
     *
     * @param   array   $columns
     * @access  public
     * @return  array
     */
    public function job_board_category_column( $columns )
    {
        $columns[ 'category_column' ] = __( 'Shortcode' , 'simple-job-board' );
        return $columns;
    }
    
    /**
     * Taxonomy -> Job Category ->  Add Value to New Column
     *
     * @param   string  $content
     * @param   string  $column_name
     * @param   int     $term_id
     * @access  public
     * @return  string
     */
    public function job_board_category_column_value( $content, $column_name, $term_id )
    {
        $term = get_term_by( 'id', $term_id, 'jobpost_category' );

        if ( $column_name == 'category_column' ) {
            $content = '[jobpost category="' . $term->slug . '"]';
        }
        return $content;
    }
    
    /**
     * Taxonomy -> Job Type ->  Add New Column
     *
     * @param   array   $columns
     * @access  public
     * @return  array
     */
    public function job_board_job_type_column( $columns )
    {
        $columns[ 'job_type_column' ] = __( 'Shortcode' , 'simple-job-board' );
        return $columns;
    }
    
    /**
     * Taxonomy -> Job Type ->  Add Value to New Column
     *
     * @param   string  $content
     * @param   string  $column_name
     * @param   int     $term_id
     * @access  public
     * @return  string
     */
    public function job_board_job_type_column_value( $content, $column_name, $term_id )
    {
        $term = get_term_by( 'id', $term_id, 'jobpost_job_type' );
        if ( $column_name == 'job_type_column' ) {
            $content = '[jobpost type="' . $term->slug . '"]';
        }
        return $content;
    }
    
    /**
     * Taxonomy -> Job Location ->  Add New Column
     *
     * @param   array   $columns
     * @access  public
     * @return  array
     */
    public function job_board_job_location_column( $columns )
    {
        $columns[ 'job_location_column' ] = __( 'Shortcode' , 'simple-job-board' );
        return $columns;
    }
    
    /**
     * Taxonomy -> Job Location ->  Add Value to New Column
     *
     * @param   string  $content
     * @param   string  $column_name
     * @param   int     $term_id
     * @access  public
     * @return  string
     */
    public function job_board_job_location_column_value( $content, $column_name, $term_id )
    {
        $term = get_term_by( 'id', $term_id, 'jobpost_location' );

        if ( $column_name == 'job_location_column' ) {
            $content = '[jobpost location="' . $term->slug . '"]';
        }
        return $content;
    }
    
    /**
     * Delete Uploads on Applicant Deletion 
     *
     * @param   int     $postId
     * @access  public
     * @return  void
     */
    public function job_board_delete_uploads( $postId )
    {
        global $post_type;
        if ( $post_type == 'jobpost_applicants' && '' != get_post_meta( $postId, 'resume_path', TRUE ) )
            unlink( get_post_meta( $postId, 'resume_path', TRUE ) );
    }
    
    /**
     * Applicant Listing - Column Name
     *
     * @param   array   $columns
     * @access  public
     * @return  array
     */
    public function job_board_applicant_list_columns( $columns )
    {
        $columns = array (
            'cb'       => '<input type="checkbox" />',
            'title'    => __( 'Job Applied for', 'simple-job-board' ),
            'applicant'=> __( 'Applicant', 'simple-job-board' ),
            'taxonomy' => __( 'Categories', 'simple-job-board' ),
            'date'     => __( 'Date', 'simple-job-board' ),
        );
        return $columns;
    }
    
    /**
     * Applicant Listing - Column Value
     *
     * @param   array   $columns
     * @param   int     $post_id
     * @access  public
     * @return  void
     */
    // 
    public function job_board_applicant_list_columns_value( $column, $post_id )
    {
        $keys = get_post_custom_keys( $post_id );
        switch ( $column ) {
            case 'applicant' :
                $applicant_name = sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( array ( 'post' => $post_id, 'action' => 'edit' ), 'post.php' ) ), esc_html( get_post_meta( $post_id, $keys[ 0 ], TRUE ) )
                );
                echo $applicant_name;
                break;
            case 'taxonomy' :
                $parent_id = wp_get_post_parent_id( $post_id ); // get_post_field ( 'post_parent', $post_id );
                $terms = get_the_terms( $parent_id, 'jobpost_category' );
                if ( ! empty( $terms ) ) {
                    $out = array ();
                    foreach ( $terms as $term ) {
                        $out[] = sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( array ( 'post_type' => get_post_type( $parent_id ), 'jobpost_category' => $term->slug ), 'edit.php' ) ), esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'jobpost_category', 'display' ) )
                        );
                    }
                    echo join( ', ', $out );
                }/* If no terms were found, output a default message. */ else {
                    _e( 'No Categories' , 'simple-job-board');
                }
                break;
        }
    }
    
}
new Simple_Job_Board_Post_Types();