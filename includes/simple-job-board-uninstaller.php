<?php

// Delete Options on deactivation
function job_board_uninstall ()
{
    // If uninstall is not called from WordPress, exit
    if ( !defined ( 'WP_UNINSTALL_PLUGIN' ) ) {
        exit ();
    }

    delete_option ( 'job_board_category_filter' );
    delete_option ( 'job_board_jobtype_filter' );
    delete_option ( 'job_board_location_filter' );
    delete_option ( 'job_board_search_bar' );

    delete_option ( 'job_board_admin_notification' );
    delete_option ( 'job_board_applicant_notification' );
    delete_option ( 'job_board_hr_notification' );

    delete_option ( 'jobapp_settings_options' );
    delete_option ( 'jobfeature_settings_options' );
    delete_option ( 'settings_hr_email' );
}

// Hook- >  Delete Options on deactivation
register_uninstall_hook ( SIMPLE_JOB_BOARD_PLUGIN_DIR . '/simple-job-board-uninstaller.php', 'job_board_uninstall' );
