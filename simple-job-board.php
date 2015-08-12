<?php
/**
 * Plugin Name:  Simple Job Board
 * Plugin URI: http://presstigers.com
 * Description: This plugin is used to create a job board for your website in a simple and elegant way.
 * Version: 2.0
 * Author: PressTigers
 * Author URI: http://presstigers.com
 * Text Domain: presstigers
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'SIMPLE_JOB_BOARD_VERSION', '2.0' );
define( 'SIMPLE_JOB_BOARD_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'SIMPLE_JOB_BOARD_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
                
/* Load Admin Styles & Scripts */
function admin_scripts ( $hook )
{
    wp_enqueue_style( 'simple-job-board-admin', SIMPLE_JOB_BOARD_PLUGIN_URL . '/assets/css/admin-styles.css' );
    
    if ( 'jobpost_page_job-board-settings' != $hook ) {
        return;
    }
    
    wp_register_script ( 'simple-job-board-ajax-setting-forms', SIMPLE_JOB_BOARD_PLUGIN_URL . '/assets/js/admin-scripts.js', array( 'jquery' ), '1.0.0', TRUE );    
    wp_enqueue_script ( 'simple-job-board-ajax-setting-forms' );
}

add_action ( 'admin_enqueue_scripts', 'admin_scripts' );


/* Load Front-end Styles & Scripts */
function frontend_scripts ()
{
    global $post;
    if ( !empty ( $post ) ):
        if ( (is_single () and $post->post_type == 'jobpost') or has_shortcode ( $post->post_content, 'jobpost' ) ): //Add scripts on plugin pages only.
            wp_enqueue_style ( 'simple-job-board-jqueryui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
            wp_enqueue_style ( 'simple-job-board-google-fonts', 'http://fonts.googleapis.com/css?family=Open+Sans'  );
            wp_enqueue_style ( 'simple-job-board-fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css'  );
            
            wp_enqueue_style( 'simple-job-board-frontend', SIMPLE_JOB_BOARD_PLUGIN_URL . '/assets/css/styles.css' );
            
            wp_register_script( 'simple-job-board-ajax-application-form', SIMPLE_JOB_BOARD_PLUGIN_URL . '/assets/js/scripts.js', array( 'jquery', 'jquery-ui-datepicker' ), '1.0.0', TRUE );
            wp_enqueue_script( 'simple-job-board-ajax-application-form' );
            
            wp_localize_script ( 'simple-job-board-ajax-application-form', 'application_form', array (
                    'ajaxurl' => admin_url ( 'admin-ajax.php' )
                )
            );
            

        endif;
    endif;
}

/* Hook - Load Front-end Styles & Scripts */
add_action ( 'wp_enqueue_scripts', 'frontend_scripts' );

/* Installer */
require_once 'includes/simple-job-board-installer.php';

/* UnInstaller */
require_once 'includes/simple-job-board-uninstaller.php';

/* ADMIN Settings */
require_once 'includes/admin/simple-job-board-admin-settings.php';

/* ADMIN Functions */
require_once 'includes/admin/simple-job-board-admin-functions.php';

/* Custom Post Types */
require_once 'includes/simple-job-board-post-types.php';

/* Forms */
require_once 'includes/forms/job-filter-form.php';

/* Shortcode */
require_once 'includes/simple-job-board-shortcode.php';

/* AJAX Callbacks */
require_once 'includes/simple-job-board-ajax.php';

/* General Functions */
require_once 'includes/simple-job-board-general.php';

/* Notifications */
require_once 'includes/simple-job-board-notification.php';

/* Meta Boxes For Application Form & Job Features */
require_once('templates/admin/job-meta-boxes.php');

/* Applicants Detail Page Under Job Board  */
require_once('templates/admin/content-single-applicant.php');

/* Include Jobpost Frontend Template */
require_once 'simple-job-board-template.php';