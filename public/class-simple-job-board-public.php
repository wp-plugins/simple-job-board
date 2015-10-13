<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/simple-job-board
 * @since      1.0.0
 *
 * @package    Simple_Job_Board
 * @subpackage Simple_Job_Board/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Simple_Job_Board
 * @subpackage Simple_Job_Board/public
 * @author     PressTigers <support@presstigers.com>
 */
class Simple_Job_Board_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $simple_job_board    The ID of this plugin.
     */
    private $simple_job_board;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $simple_job_board       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct ( $simple_job_board, $version )
    {

        $this->simple_job_board = $simple_job_board;
        $this->version = $version;

        /**
         * The class responsible for defining all the custom post types in the admin area.
         */
        require_once plugin_dir_path ( dirname ( __FILE__ ) ) . 'includes/class-simple-job-board-post-types.php';

        /**
         * The class responsible for defining all the shortcodes in the front end area.
         */
        require_once plugin_dir_path ( dirname ( __FILE__ ) ) . 'includes/class-simple-job-board-shortcodes.php';

        /**
         * The class responsible for Ajax Call on Job Submission in the front end area.
         */
        require_once plugin_dir_path ( dirname ( __FILE__ ) ) . 'includes/class-simple-job-board-ajax.php';
        
        /**
         * The class responsible for Sending email notificatins to Applicant, Admin & HR.
         */
        require_once plugin_dir_path ( dirname ( __FILE__ ) ) . 'includes/class-simple-job-board-notification.php';

        add_action ( 'after_setup_theme', array ( $this, 'include_template_functions' ), 11 );       
        
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles ()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Simple_Job_Board_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Simple_Job_Board_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style ( $this->simple_job_board . '-bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css', array (), $this->version, 'all' );
        wp_enqueue_style ( $this->simple_job_board . '-jqueryui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
        wp_enqueue_style ( $this->simple_job_board . '-google-font', 'https://fonts.googleapis.com/css?family=Lato:400,100,100italic,300,300italic,400italic,700,700italic&subset=latin,latin-ext', array (), $this->version, 'all' );
        wp_enqueue_style ( $this->simple_job_board . '-frontend', plugin_dir_url ( __FILE__ ) . 'css/simple-job-board-public.css', array (), $this->version, 'all' );
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts ()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Simple_Job_Board_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Simple_Job_Board_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script ( $this->simple_job_board . '-bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js', array ( 'jquery' ), $this->version, TRUE );
        wp_enqueue_script ( $this->simple_job_board . 'front-end', plugin_dir_url ( __FILE__ ) . 'js/simple-job-board-public.js', array ( 'jquery', 'jquery-ui-datepicker' ), $this->version, TRUE );
        wp_localize_script ( $this->simple_job_board . 'front-end', 'application_form', array (
            'ajaxurl'              => admin_url ( 'admin-ajax.php' ),
            'setting_extensions'   => get_option ( 'job_board_upload_file_ext' ),
            'all_extensions_check' => get_option ( 'job_board_all_extensions_check' ),
            'allowed_extensions'   => get_option ( 'job_board_allowed_extensions' ),
            'job_listing_view'     => get_option ( 'job_board_listing' ),
                )
        );
    }

    /**
     * Load Templates
     * 
     * @since    2.1.0
     */
    public function include_template_functions ()
    {
        include( 'partials/simple-job-board-template.php' );        
    }

}