<?php

/* Disable Custom Fields from Job Posts */
function job_board_remove_custom_fields ()
{
    remove_meta_box ( 'postcustom', 'jobpost', 'normal' );
}

add_action ( 'admin_menu', 'job_board_remove_custom_fields' );

// Settings - Taxonomies Under Custom Post Jobs
function job_board_default_options ()
{
    add_option ( 'job_board_category_filter', 'yes', '' );
    add_option ( 'job_board_jobtype_filter', 'yes', '' );
    add_option ( 'job_board_location_filter', 'yes', '' );
    add_option ( 'job_board_search_bar', 'yes', '' );

    add_option ( 'job_board_admin_notification', 'yes' );
    add_option ( 'job_board_applicant_notification', 'yes' );
    add_option ( 'job_board_hr_notification', 'no' );
}

/* Fixing rewrite rules on plugin activation */
function job_board_activate ()
{
    job_board_register ();
    job_board_default_options ();
    flush_rewrite_rules ();
}

register_activation_hook ( SIMPLE_JOB_BOARD_PLUGIN_DIR . '/simple-job-board.php', 'job_board_activate' );