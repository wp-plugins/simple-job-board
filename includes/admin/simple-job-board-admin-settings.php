<?php

// Job Settings Submenu
function jobpost_setting_page_menu ()
{
    add_submenu_page ( 'edit.php?post_type=jobpost', 'Settings', 'Settings', 'manage_options', 'job-board-settings', 'settings_tab_menu' );
}

// Hook - Job Settings Submenu
add_action ( 'admin_menu', 'jobpost_setting_page_menu' );