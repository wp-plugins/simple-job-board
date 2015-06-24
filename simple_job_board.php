<?php
/**
 * Plugin Name:  Simple Job Board
 * Plugin URI: http://presstigers.com
 * Description: This plugin is used to create a job board for your website in a simple and elegant way.
 * Version: 1.1
 * Author: PressTigers
 * Author URI: http://presstigers.com
 * Text Domain: presstigers
 * License: GPL2
 */
$plugin_path = plugin_dir_path( __FILE__ ); //Plugin directory absolute path.

/*Include Plugin Functions library*/
require_once 'lib/functions.php';

/*Include Jobpost Editor Meta Boxes for Application form and Job Features*/
require_once('view/jobpost_editor.php');

/*Include Jobpost Frontend Template*/
require_once 'view/single_jobpost.php';

/*Ajax Application Form Handler*/
require_once('lib/process_application_form.php');


/*Creating Applicants detail page in dashboard*/
require_once('view/applicant_detail_page.php');

/*Load dashboard style*/
function jobpost_load_admin_style() {
    wp_enqueue_style( 'admin_jobpost_css', plugins_url(  'assets/css/admin_style.css', __FILE__ ));
}
add_action( 'admin_enqueue_scripts', 'jobpost_load_admin_style' );

/*Load front-end styles*/
function jobpost_plugin_scripts(){
    global $post;
    if(!empty($post)):
        if((is_single() and $post->post_type=='jobpost') or has_shortcode($post->post_content, 'jobpost')): //Add scripts on plugin pages only.
            wp_enqueue_style('jobpost_CSS', plugins_url( 'assets/css/style_jobpost.css', __FILE__ ));
            wp_enqueue_script( 'my-ajax-request', plugin_dir_url( __FILE__ ) . 'assets/js/custom.js', array( 'jquery' ));
            wp_localize_script( 'my-ajax-request', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));

            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        endif;
    endif;
}add_action( 'wp_enqueue_scripts', 'jobpost_plugin_scripts' );