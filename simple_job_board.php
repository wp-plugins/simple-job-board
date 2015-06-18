<?php
/**
 * Plugin Name:  Simple Job Board
 * Plugin URI: http://presstigers.com
 * Description: This plugin is used to create a job board for your website in a simple and elegant way.
 * Version: 1.0
 * Author: PressTigers
 * Author URI: http://presstigers.com
 * Text Domain: presstigers
 * License: GPL2
 */
$plugin_path = plugin_dir_path( __FILE__ ); //Plugin directory absolute path.

function remove_post_custom_fields() {
	remove_meta_box( 'postcustom' , 'jobpost' , 'normal' ); 
}
add_action( 'admin_menu' , 'remove_post_custom_fields' );

/*Include Plugin Functions library*/
require_once 'lib/functions.php';

/*Include Jobpost Editor Meta Boxes for Application form and Job Features*/
require_once('view/jobpost_editor.php');

/*Include Jobpost Frontend Template*/
require_once 'view/single_jobpost.php';

/*Ajax Application Form Handler*/
require_once('lib/process_application_form.php');


/*Creating Applicants detail page*/
require_once($plugin_path.'view/applicant_detail_page.php');

function load_admin_style() {
    wp_enqueue_style( 'admin_jobpost_css', plugins_url(  'assets/css/admin_style.css', __FILE__ ));
}
add_action( 'admin_enqueue_scripts', 'load_admin_style' );

function jobpost_myplugin_activate() {
    flush_rewrite_rules();
//    $admins = get_role( 'administrator' );// gets the administrator role
//    $admins->remove_cap( 'read_applicant' );
}
register_activation_hook( __FILE__, 'jobpost_myplugin_activate' );

function plugin_scripts(){
    global $post;
    if(!empty($post)):
        if((is_single() and $post->post_type=='jobpost') or has_shortcode($post->post_content, 'jobpost')):
            wp_enqueue_style('jobpost_CSS', plugins_url( 'assets/css/style_jobpost.css', __FILE__ ));
            wp_enqueue_script( 'my-ajax-request', plugin_dir_url( __FILE__ ) . 'assets/js/custom.js', array( 'jquery' ) );
            wp_localize_script( 'my-ajax-request', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );     

            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        endif;
    endif;
}
add_action( 'wp_enqueue_scripts', 'plugin_scripts' );

function register_jobpost(){
    $labels=array(
        'add_new_item'  => 'Add New Job',
        'all_items' => 'All Jobs',
    );
    $args=array(
        'label' => __( 'Job Board', 'presstigers' ),
        'labels'=> $labels,
        'public'=>  true,
        'show_in_nav_menus' => false,
        'menu_icon'  => 'dashicons-clipboard',
        'description' => __( 'Job Posting', 'presstigers', 'custom-fields' ),
        'supports' => array('title', 'editor'),
        'rewrite' => array('slug'=>'jobs'),
    );
    register_post_type('jobpost',$args);
}
add_action( 'init', 'register_jobpost' );

function jobpost_applicants(){
    $lables= array('edit_item'=>'Update Application');
    $args=array(
        'label' => __( 'Applicants', 'presstigers' ),
        'labels' => $lables,
        'public'            =>  true,
        'show_in_nav_menus' => false,
        'exclude_from_search'=> true,
        'publicly_queryable' => false,
        'map_meta_cap'      => true,
        'show_in_menu' => 'edit.php?post_type=jobpost',
        'description' => __( 'List of Applicants with their resume', 'presstigers' ),
        'supports' => array('editor'),
        'capabilities' => array(
            'create_posts' => false,
        )
);
    register_post_type('jobpost_applicants',$args);
}
add_action( 'init', 'jobpost_applicants' );

/**
 * Shortcode Generator
 * @param type $atts
 * @return type
 */
function jobpost_func( $atts ) {
    $a = shortcode_atts( array(
        'posts' => '-1',
        'excerpt' => 'yes',
    ), $atts );
    
    $args=array(
        'posts_per_page'   => $a['posts'],
        'post_type'     =>'jobpost',
        );
    query_posts( $args );
    function custom_excerpt_more( $more ) {
	return '....';
    }
    add_filter( 'excerpt_more', 'custom_excerpt_more' );
    ob_start();
    echo '<ol class="jobpost_list">';
    if(have_posts()): while(have_posts()): the_post();
    ?>
        <li>
            <strong><?php the_title(); ?></strong>
            <?php if($a['excerpt'] == 'yes') the_excerpt(); ?>
            <a href="<?php the_permalink() ?>">Job Detail</a>
        </li>
    <?php
    endwhile; endif;
    echo '</ol>';
    $html=ob_get_clean();
    wp_reset_query();
    return $html;
}
add_shortcode( 'jobpost', 'jobpost_func' );

/**Display Applicants Custom Columns**/
add_filter( 'manage_edit-jobpost_applicants_columns', 'jobpost_applicants_columns' ) ;
add_action( 'manage_jobpost_applicants_posts_custom_column' , 'custom_jobpost_applicant_column', 10, 2 );

function jobpost_applicants_columns( $columns ) {
	$columns = array(
            'applicant' => __( 'Applicant', 'presstigers' ),
            'job' => __( 'Job Applied For', 'presstigers' ),
	);
	return $columns;
}

function custom_jobpost_applicant_column( $column, $post_id ) {
    $keys=  get_post_custom_keys($post_id);
    switch ( $column ) {
        case 'applicant' :
            echo '<a class="row-title" href="'.admin_url().'post.php?post='.$post_id.'&action=edit">'.get_post_meta( $post_id , $keys[0] , true ).'</a>';
            break;
        case 'job' :
            echo '<a class="row-title" href="'.admin_url().'post.php?post='.  get_post_field('post_parent', $post_id).'&action=edit">'.get_the_title( $post_id ).'</a>'; 
            break;
        
    }
}
