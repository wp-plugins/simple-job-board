<?php

/**
 * Add Settings Tab Menu.
 */
function settings_tab_menu ()
{
    $active_tab = isset ( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'job_features';
    ?>
    <div class="wrap">
        <h1>Settings</h1>        
        <h2 class="nav-tab-wrapper">
            <a href="#settings-job_features" class="nav-tab  <?php echo $active_tab == 'job_features' ? 'nav-tab-active' : ''; ?>">Job Features</a>
            <a href="#settings-application_form_fields" class="nav-tab <?php echo $active_tab == 'application_form_fields' ? 'nav-tab-active' : ''; ?>">Application Form Fields</a>
            <a href="#settings-job_filters" class="nav-tab <?php echo $active_tab == 'job_filters' ? 'nav-tab-active' : ''; ?>">Filters</a>
            <a href="#settings-email_notifications" class="nav-tab <?php echo $active_tab == 'email_notifications' ? 'nav-tab-active' : ''; ?>">Email Notifications</a>
        </h2>

        <!-- Job Features -->
        <div id="settings-job_features" class="settings_panel" style="display: none;">
            <h4>Default Feature List</h4>
            <div class="app_form_fields jobpost_fields">
                <form method="post" action="" id="job_feature_form">
                    <ul id="settings_job_features">
                        <?php
                        /* Save Form Data to Wordpress Option */
                        if ( isset ( $_POST[ 'jobfeature' ] ) && $_POST[ 'jobfeature' ] != '' && isset ( $_POST[ 'jobfeature_value' ] ) ) {

                            $option_name = 'jobfeature_settings_options';
                            $option_data = array_combine ( $_POST[ 'jobfeature' ], $_POST[ 'jobfeature_value' ] );
                            $serialized_option = serialize ( $option_data );

                            if ( FALSE !== get_option ( 'jobfeature_settings_options' ) ) {

                                //update field option array
                                update_option ( $option_name, $serialized_option );
                            } else {

                                // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                                $deprecated = NULL;
                                $autoload = 'no';
                                add_option ( $option_name, $serialized_option, $deprecated, $autoload );
                            }
                        } elseif ( isset ( $_POST[ 'empty_feature' ] ) && 'empty_features' == $_POST[ 'empty_feature' ] ) {
                            update_option ( 'jobfeature_settings_options', '' );
                        }

                        // Display Settings Option Feature
                        $options_data = get_option ( 'jobfeature_settings_options' );
                        $fields = unserialize ( $options_data );
                        if ( NULL != $fields ):
                            foreach ( $fields as $field => $val ) {
                                if ( 'empty' === $val ) {
                                    $feature_value = '<input type="text" value=" "  name="jobfeature_value[]" />';
                                } else {
                                    $feature_value = '<input type="text" value="' . $val . '"  name="jobfeature_value[]" />';
                                }
                                echo '<li class="' . $field . '"><label for="' . $field . '"><strong>Field Name:</strong> ' . ucwords ( str_replace ( '_', ' ', substr ( $field, 11 ) ) ) . '</label> <input type="hidden"  name="jobfeature[]" value="' . $field . '"  >' . $feature_value . ' &nbsp; <div class="button removeField" >Delete</div></li>';
                            }
                        endif;
                        ?>
                    </ul>
                    <input type="hidden" name="empty_feature" value="empty_features" />
                    <input type="hidden" value="1" name="admin_notices" />
                </form>
            </div> 

            <h4>Add New Feature</h4>
            <div class="app_form_fields">
                <table id="jobfeatures_form" class="alignleft">
                    <thead>
                        <tr>
                            <th>Feature</th>
                            <th>Value</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>                        
                        <tr>
                            <td class="left" id="jobFeature"><input type="text" id="settings_jobfeature_name" /></td>
                            <td ><input type="text" id="settings_jobfeature_value" /></td>
                            <td><div class="button" id="settings_addFeature">Add Field</div></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <input type="submit" name="jobfeature_submit" id="jobfeature_form" class="button button-primary" value="Save Changes" />
        </div>

        <!-- Application Form Fields -->
        <div id="settings-application_form_fields" class="settings_panel" style="display: none;">
            <?php
            global $jobfields;
            $field_types = array (
                'text' => 'Text Field',
                'text_area' => 'Text Area',
                'date' => 'Date',
                'checkbox' => 'Check Box',
                'dropdown' => 'Drop Down',
                'radio' => 'Radio'
            );
            ?>
            <h4>Default Application Form  Fields</h4>
            <div class="app_form_fields jobpost_fields">
                <form method="post" id="job_app_form">
                    <ul id="settings_app_form_fields">
                        <?php
                        // Save the Form Data
                        if ( isset ( $_POST[ 'field_name' ] ) && isset ( $_POST[ 'field_type' ] ) ) {

                            $jopapp_fields = mergeArrays ( $_POST[ 'field_name' ], $_POST[ 'field_type' ], $_POST[ 'field_option' ] );

                            // Creating WP Options For Job Application
                            $jobapp_option_name = 'jobapp_settings_options';
                            $jobapp_option_data = $jopapp_fields;
                            $jobapp_serialized_option = serialize ( $jobapp_option_data );

                            if ( FALSE !== get_option ( 'jobapp_settings_options' ) ) {

                                // Update Option 
                                update_option ( $jobapp_option_name, $jobapp_serialized_option );
                            } else {

                                // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                                $deprecated = NULL;
                                $autoload = 'no';
                                add_option ( $jobapp_option_name, $jobapp_serialized_option, $deprecated, $autoload );
                            }
                        } elseif ( isset ( $_POST[ 'empty_jobapp' ] ) && 'empty_jobapp' == $_POST[ 'empty_jobapp' ] ) {
                            update_option ( 'jobapp_settings_options', '' );
                        }

                        $jobapp_setting_fields = unserialize ( get_option ( 'jobapp_settings_options' ) );

                        if ( NULL != $jobapp_setting_fields ):
                            foreach ( $jobapp_setting_fields as $key => $val ):
                                if ( isset ( $val[ 'type' ] ) && isset ( $val[ 'option' ] ) ):
                                    if ( substr ( $key, 0, 7 ) == 'jobapp_' ):
                                        $select_option = NULL;

                                        // Job Application Form Selected Field
                                        foreach ( $field_types as $field_key => $field_val ) {
                                            if ( $val[ 'type' ] == $field_key )
                                                $select_option .= '<option value="' . $field_key . '" selected>' . $field_val . '</option>';
                                        }

                                        // Options for [Checkbox],[Radio],[Drop Down] Fields
                                        if ( !( 'text' === $val[ 'type' ] or 'date' === $val[ 'type' ] or 'text_area' === $val[ 'type' ] ) ):
                                            $field_options = '<input type="text" name="field_option[]" value="' . $val[ 'option' ] . '"  placeholder="Option1, option2, option3" />';
                                        else:
                                            $field_options = '<input type="hidden" name="field_option[]" value="' . $val[ 'option' ] . '" placeholder="Option1, option2, option3"  />';

                                        endif;

                                        echo '<li class="' . $key . '">'
                                        . '<label for="">' . ucwords ( str_replace ( '_', ' ', substr ( $key, 7 ) ) ) . '</label>'
                                        . '<input type="hidden" name="field_name[]" value="' . $key . '" >'
                                        . '<input type="hidden" name="field_type[]" value="' . $val[ 'type' ] . '" >'
                                        . '' . $field_options . ''
                                        . '<select class="jobapp_field_type" name="' . $key . '[type]">' . $select_option . '</select>';

                                        echo ' &nbsp; <div class="button removeField">Delete</div></li>';
                                    endif;
                                endif;
                            endforeach;
                        endif;
                        ?>
                    </ul>
                    <input type="hidden" name="empty_jobapp" value="empty_jobapp" />
                    <input type="hidden" value="1" name="admin_notices" />
                </form>
            </div>
            <div class="clearfix clear"></div>

            <div class="app_form_fields">
                <table id="jobapp_form_fields" class="alignleft">
                    <thead>
                        <tr>
                            <th class="left">Field</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="left" id="newmetaleft"><input type="text" id="setting_jobapp_name" /></td>
                            <td>
                                <select id="setting_jobapp_field_type">
                                    <?php
                                    foreach ( $field_types as $key => $val ):
                                        echo '<option value="' . $key . '" class="' . $key . '">' . $val . '</option>';
                                    endforeach;
                                    ?>
                                </select>
                                <input id="settings_jobapp_field_options" class="jobapp_field_type" type="text" style="display: none;" placeholder="Option1, Option2, Option3" >
                            </td>
                            <td><div class="button" id="app_add_field">Add Field</div></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <input type="submit" name="jobapp_submit" id="jobapp_btn" class="button button-primary" value="Save Changes" />
        </div>

        <!-- Filters Setting -->
        <div id="settings-job_filters" class="settings_panel" style="display: none;">
            <?php
            if ( ( isset ( $_POST[ 'job_filters' ] ) && '' !== $_POST[ 'job_filters' ] ) || isset ( $_POST[ 'empty_filter' ] ) ) {

                // Empty  checkboxes status
                $category_status = 0;
                $jobtype_status = 0;
                $location_status = 0;
                $search_bar_status = 0;

                // Update checkbox status 
                if ( isset ( $_POST[ 'job_filters' ] ) && '' != $_POST[ 'job_filters' ] ) {
                    foreach ( $_POST[ 'job_filters' ] as $filter ) {
                        if ( 'category' === $filter ) {
                            update_option ( 'job_board_category_filter', 'yes' );
                            $category_status = 1;
                        } elseif ( 'jobtype' === $filter ) {
                            update_option ( 'job_board_jobtype_filter', 'yes' );
                            $jobtype_status = 1;
                        } elseif ( 'location' === $filter ) {
                            update_option ( 'job_board_location_filter', 'yes' );
                            $location_status = 1;
                        } elseif ( 'search_bar' === $filter ) {
                            update_option ( 'job_board_search_bar', 'yes' );
                            $search_bar_status = 1;
                        }
                    }
                }

                // Setting filter's value to 'no' that are not set
                if ( 0 === $category_status )
                    update_option ( 'job_board_category_filter', 'no' );
                if ( 0 === $jobtype_status )
                    update_option ( 'job_board_jobtype_filter', 'no' );
                if ( 0 === $location_status )
                    update_option ( 'job_board_location_filter', 'no' );
                if ( 0 === $search_bar_status )
                    update_option ( 'job_board_search_bar', 'no' );
            }
            ?>
            <h4>Select filters that display on front-end</h4>
            <form method="post" id="job_filters_form">
                <div class="app_form_fields">
                    <p>
                        <input type="checkbox" name="job_filters[]" value="category"  <?php if ( 'yes' === get_option ( 'job_board_category_filter' ) ) echo 'checked="checked"'; ?> />
                        <label>Enable the Job Category Filter</label>
                        <input type='hidden' name="empty_filter[]" value="empty_category" >
                    </p>
                    <p>
                        <input type="checkbox" name="job_filters[]" value="jobtype" <?php if ( 'yes' === get_option ( 'job_board_jobtype_filter' ) ) echo 'checked="checked"'; ?>/>
                        <label>Enable the Job Type Filter</label>
                        <input type='hidden' name="empty_filter[]" value="empty_jobtype" >
                    </p>
                    <p>
                        <input type="checkbox" name="job_filters[]" value="location" <?php if ( 'yes' === get_option ( 'job_board_location_filter' ) ) echo 'checked="checked"'; ?>/>
                        <label>Enable the Job Location Filter</label>
                        <input type='hidden' name="empty_filter[]" value="empty_location" >
                    </p>
                    <p>
                        <input type="checkbox" name="job_filters[]" value="search_bar" <?php if ( 'yes' === get_option ( 'job_board_search_bar' ) ) echo 'checked="checked"'; ?>/>
                        <label>Enable the Search Bar</label>
                        <input type='hidden' name="empty_filter[]" value="empty_search_bar" >
                    </p>
                </div>
                <input type="hidden" value="1" name="admin_notices" />
                <input type="submit" name="jobfilter_submit" id="job_filters" class="button button-primary" value="Save Changes" />
            </form>
        </div>

        <!-- Notification -->
        <div id="settings-email_notifications" class="settings_panel" style="display: none;">
            <?php
            $hr_email = ( false !== get_option ( 'settings_hr_email' ) ) ? get_option ( 'settings_hr_email' ) : '';
            if ( (isset ( $_POST[ 'email_notification' ] ) && '' != $_POST[ 'email_notification' ]) || isset ( $_POST[ 'empty_form_check' ] ) ) {

                // Empty  checkboxes status
                $hr_email_status = 'no';
                $admin_email_status = 'no';
                $applicant_email_status = 'no';
                if ( isset ( $_POST[ 'email_notification' ] ) ) {
                    foreach ( $_POST[ 'email_notification' ] as $value ) {
                        if ( 'hr_email' === $value ) {
                            update_option ( 'job_board_hr_notification', 'yes' );
                            $hr_email_status = 'yes';
                        } elseif ( 'admin_email' === $value ) {
                            update_option ( 'job_board_admin_notification', 'yes' );
                            $admin_email_status = 'yes';
                        } elseif ( 'applicant_email' === $value ) {
                            update_option ( 'job_board_applicant_notification', 'yes' );
                            $applicant_email_status = 'yes';
                        }
                    }
                }

                if ( isset ( $_POST[ 'hr_email' ] ) ) {
                    ( false !== get_option ( 'settings_hr_email' ) ) ? update_option ( 'settings_hr_email', $_POST[ 'hr_email' ] ) : add_option ( 'settings_hr_email', $_POST[ 'hr_email' ], '' );
                    $hr_email = get_option ( 'settings_hr_email' );
                }

                // Setting filter's value to 'no' that are not set
                if ( 'no' === $hr_email_status )
                    update_option ( 'job_board_hr_notification', 'no' );
                if ( 'no' === $admin_email_status )
                    update_option ( 'job_board_admin_notification', 'no' );
                if ( 'no' === $applicant_email_status )
                    update_option ( 'job_board_applicant_notification', 'no' );
            }
            ?>
            <h4>Enable Email Notification</h4>
            <form method="post" id="email_notification_form">
                <div class="app_form_fields">
                    <table>
                        <tr>
                            <th>HR Email:<input type="hidden" name="empty_form_check" value="empty_form_submitted"></th>
                            <td><input type="email" name="hr_email" value="<?php echo $hr_email ?>" size="30"></td>
                        </tr>
                        <tr>
                            <th></th>
                            <td><input type="checkbox" name="email_notification[]" value="hr_email" <?php if ( 'yes' === get_option ( 'job_board_hr_notification' ) ) echo 'checked="checked"'; ?>/>Enable the HR email notification<br /><br /></td>
                        </tr>
                        <tr>
                            <th>Admin Email:</th>
                            <td><input type="text" value="<?php echo get_option ( 'admin_email' ); ?>" size="30" readonly></td>
                        </tr>
                        <tr>
                            <th></th>
                            <td><input type="checkbox" name="email_notification[]" value="admin_email" <?php if ( 'yes' === get_option ( 'job_board_admin_notification' ) ) echo 'checked="checked"'; ?> />Enable the Admin email notification</td>
                        </tr>
                        <tr>
                            <th></th>
                            <td><input type="checkbox" name="email_notification[]" value="applicant_email" <?php if ( 'yes' === get_option ( 'job_board_applicant_notification' ) ) echo 'checked="checked"'; ?>/>Enable the Applicant email notification</td>
                        </tr>
                    </table>
                </div>
                <input type="hidden" value="1" name="admin_notices" />
                <input type="submit" name="job_email_notification" id="job_email_notification" class="button button-primary" value="Save Changes" />
            </form>
        </div>
    </div>
    <?php
}

// Settings Notification
function settings_notification ()
{

    if ( isset ( $_POST[ 'admin_notices' ] ) ) {
        ?>

        <div class="updated">
            <p><?php _e ( 'Settings saved.', 'presstigers' ); ?></p>
        </div>
        <?php
    }
}

// Hook - Settings Notification
add_action ( 'admin_notices', "settings_notification" );
