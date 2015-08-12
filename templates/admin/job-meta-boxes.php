<?php

/**
 * Adds Meta boxes to the main column on the jobpost edit screens.
 */
function job_baord_meta_boxes ()
{

    add_meta_box (
            'jobpost_metas', __ ( 'Job Features', 'wpquantum' ), 'job_board_meta_job_features', 'jobpost'
    );

    add_meta_box (
            'jobpost_application_fields', __ ( 'Application Form Fields', 'wpquantum'
            ), 'job_board_meta_application_form', 'jobpost'
    );
}

add_action ( 'add_meta_boxes', 'job_baord_meta_boxes' );

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function job_board_meta_job_features ( $post )
{
    global $jobfields;

    // Add a nonce field so we can check for it later.
    wp_nonce_field ( 'myplugin_jobpost_meta_awesome_box', 'jobpost_meta_box_nonce' );

    /*
     * Use get_post_meta() to retrieve an existing value
     * from the database and use the value for the form.
     */
    ?>
    <div class="job_features meta_option_panel jobpost_fields">
        <ul id="job_features">
            <?php
            $keys = get_post_custom_keys ( $post->ID );

            //getting setting page saved options
            $settings_options = unserialize ( get_option ( 'jobfeature_settings_options' ) );

            //check Array differnce when $keys is not NULL
            if ( NULL == $keys ) {

                //"Add New" job check
                $removed_options = $settings_options;
            } elseif ( NULL == $settings_options ) {
                $removed_options = '';
            } else {

                //Remove the same option from post meta and options
                $removed_options = array_diff_key ( $settings_options, get_post_meta ( $post->ID ) );
            }

            if ( NULL != $keys ):
                foreach ( $keys as $key ):
                    if ( substr ( $key, 0, 11 ) == 'jobfeature_' ) {
                        $val = get_post_meta ( $post->ID, $key, TRUE );
                        echo '<li><label for="' . $key . '">';
                        _e ( ucwords ( str_replace ( '_', ' ', substr ( $key, 11 ) ) ), 'wpquantum' );
                        echo '</label> ';

                        // Setting options meta Fileds button to Empty
                        $button = '<div class="button removeField">Delete</div>';
                        echo '<input type="text" id="' . $key . '" name="' . $key . '" value="' . esc_attr ( $val ) . '" /> &nbsp; ' . $button . '</li>';
                    }
                endforeach;
            endif;

            // Adding setting page features to jobpost
            if ( NULL != $removed_options ):
                if ( !isset ( $_GET[ 'action' ] ) ):

                    foreach ( $removed_options as $key => $val ):
                        if ( 'empty' === $val ) {
                            $val = ''; // Conver Empty Value Parameter to NULL 
                        }

                        if ( substr ( $key, 0, 11 ) == 'jobfeature_' ) {
                            echo '<li><label for="' . $key . '">';
                            _e ( ucwords ( str_replace ( '_', ' ', substr ( $key, 11 ) ) ), 'wpquantum' );
                            echo '</label> ';
                            echo '<input type="text" id="' . $key . '" name="' . $key . '" value="' . esc_attr ( $val ) . '" /> &nbsp; <div class="button removeField">Delete</div></li>';
                        }
                    endforeach;
                endif;
            endif;
            ?>
        </ul>
    </div>
    <div class="clearfix clear"></div>
    <table id="jobfeatures_form" class="alignleft">
        <thead>
            <tr>
                <th><label for="jobFeature">Feature</label></th>
                <th><label for="jobFeatureVal">Value</label></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td id="jobFeature"><input type="text" id="jobfeature_name" /></td>
                <td><input type="text" id="jobfeature_value" /></td>
                <td><div class="button" id="addFeature">Add Field</div></td>
            </tr>
        </tbody>
    </table>
    <div class="clearfix clear"></div>
    <?php
}

function job_board_meta_application_form ( $post )
{
    global $jobfields;

    // Add a nonce field so we can check for it later.
    wp_nonce_field ( 'myplugin_jobpost_meta_awesome_box', 'jobpost_meta_box_nonce' );

    /*
     * Use get_post_meta() to retrieve an existing value
     * from the database and use the value for the form.
     */
    ?>
    <div class="meta_option_panel jobpost_fields">
        <ul id="app_form_fields">
            <?php
            $field_types = array ( 'text' => 'Text Field', 'text_area' => 'Text Area', 'date' => 'Date', 'checkbox' => 'Check Box', 'dropdown' => 'Drop Down', 'radio' => 'Radio' );
            $keys = get_post_custom_keys ( $post->ID );

            // Getting setting page saved options
            $jobapp_settings_options = unserialize ( get_option ( 'jobapp_settings_options' ) );

            //check Array differnce when $keys is not NULL
            if ( NULL == $keys ) {

                //"Add New" job Check
                $jobapp_removed_options = $jobapp_settings_options;
            } elseif ( NULL == $jobapp_settings_options ) {
                $jobapp_removed_options = '';
            } else {
                //Remove the same option from post meta and options
                $jobapp_removed_options = array_diff_key ( $jobapp_settings_options, get_post_meta ( $post->ID ) );
            }

            if ( NULL != $keys ):
                foreach ( $keys as $key ):
                    if ( substr ( $key, 0, 7 ) == 'jobapp_' ):
                        $val = get_post_meta ( $post->ID, $key, TRUE );
                        $val = unserialize ( $val );
                        $fields = NULL;
                        foreach ( $field_types as $field_key => $field_val ) {
                            if ( $val[ 'type' ] == $field_key )
                                $fields .= '<option value="' . $field_key . '" selected>' . $field_val . '</option>';
                            else
                                $fields .= '<option value="' . $field_key . '" >' . $field_val . '</option>';
                        }
                        echo '<li class="' . $key . '"><label for="">' . ucwords ( str_replace ( '_', ' ', substr ( $key, 7 ) ) ) . '</label><select class="jobapp_field_type" name="' . $key . '[type]">' . $fields . '</select>';
                        if ( !($val[ 'type' ] == 'text' or $val[ 'type' ] == 'date' or $val[ 'type' ] == 'text_area' ) ):
                            echo '<input type="text" name="' . $key . '[options]" value="' . $val[ 'options' ] . '" placeholder="Option1, option2, option3" />';
                        else:
                            echo '<input type="text" name="' . $key . '[options]" placeholder="Option1, option2, option3" style="display:none;"  />';
                        endif;
                        $button = '<div class="button removeField">Delete</div>';

                        echo ' &nbsp; ' . $button . '</li>';
                    endif;
                endforeach;
            endif;

            /**
             * options data displaying for Job App
             * Adding setting page jobapp fields to jobpost
             */
            if ( NULL != $jobapp_removed_options ):
                if ( !isset ( $_GET[ 'action' ] ) ):
                    foreach ( $jobapp_removed_options as $jobapp_field_name => $val ):
                        if ( isset ( $val[ 'type' ] ) && isset ( $val[ 'option' ] ) ):
                            if ( substr ( $jobapp_field_name, 0, 7 ) == 'jobapp_' ):
                                $fields = NULL;
                                foreach ( $field_types as $field_key => $field_val ) {
                                    if ( $val[ 'type' ] == $field_key )
                                        $fields .= '<option value="' . $field_key . '" selected>' . $field_val . '</option>';
                                    else
                                        $fields .= '<option value="' . $field_key . '" >' . $field_val . '</option>';
                                }
                                echo '<li class="' . $jobapp_field_name . '"><label for="">' . ucwords ( str_replace ( '_', ' ', substr ( $jobapp_field_name, 7 ) ) ) . '</label><select class="jobapp_field_type" name="' . $jobapp_field_name . '[type]">' . $fields . '</select>';
                                if ( !($val[ 'type' ] == 'text' or $val[ 'type' ] == 'date' or $val[ 'type' ] == 'text_area' ) ):
                                    echo '<input type="text" name="' . $jobapp_field_name . '[options]" value="' . $val[ 'option' ] . '"  placeholder="Option1, option2, option3" />';
                                else:
                                    echo '<input type="text" name="' . $jobapp_field_name . '[options]" placeholder="Option1, option2, option3" style="display:none;"  />';

                                endif;
                                echo ' &nbsp;<div class="button removeField">Delete</div></li>';
                            endif;
                        endif;
                    endforeach;
                endif;
            endif;
            ?>
        </ul>
    </div>
    <div class="clearfix clear"></div>
    <table id="jobapp_form_fields" class="alignleft">
        <thead>
            <tr>
                <th><label for="metakeyselect">Field</label></th>
                <th><label for="metavalue">Type</label></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="left" id="newmetaleft"><input type="text" id="jobapp_name" /></td>
                <td>
                    <select id="jobapp_field_type">
                        <?php
                        foreach ( $field_types as $key => $val ):
                            echo '<option value="' . $key . '" class="' . $key . '">' . $val . '</option>';
                        endforeach;
                        ?>
                    </select>
                    <input id="jobapp_field_options" class="jobapp_field_type" type="text" style="display: none;" placeholder="Option1, Option2, Option3" >
                </td>
                <td><div class="button" id="addField">Add Field</div></td>
            </tr>
        </tbody>
    </table>
    <div class="clearfix clear"></div>
    <script type="text/javascript">
        jQuery('document').ready(function ($) {
            /*Job Application Field Type change*/
            $('#jobapp_field_type').change(function () {
                var fieldType = $(this).val();

                if (!(fieldType == 'text' || fieldType == 'date' || fieldType == 'text_area')) {
                    $('#jobapp_field_options').show();
                }
                else {
                    $('#jobapp_field_options').hide();
                    $('#jobapp_field_options').val('');
                }
            });

            /*Add Application Field (Group Fields)*/
            $('#addField').click(function () {
                var fieldNameRaw = $('#jobapp_name').val(); // Get Raw value.
                var fieldNameRaw = fieldNameRaw.trim();    // Remove White Spaces from both ends.
                var fieldName = fieldNameRaw.split(' ').join('_').toLowerCase(); //Replace white space with _.
                var fieldType = $('#jobapp_field_type').val();
                var fieldOptions = $('#jobapp_field_options').val();


                var fieldTypeHtml = $('#jobapp_field_type').html();
                if (fieldName != '') {
                    if (fieldType == 'text' || fieldType == 'date' || fieldType == 'text_area') {
                        $('#app_form_fields').append('<li class="' + fieldName + '"><label for="' + fieldName + '">' + fieldNameRaw + '</label><select class="jobapp_field_type" name="jobapp_' + fieldName + '[type]">' + fieldTypeHtml + '</select> &nbsp; <div class="button removeField">Delete</div></li>');
                        $('.' + fieldName + ' .' + fieldType).attr('selected', 'selected');
                        $('#jobapp_name').val('');
                        $('#jobapp_field_type').val('text');
                    }
                    else {
                        $('#app_form_fields').append('<li class="' + fieldName + '"><label for="' + fieldName + '">' + fieldNameRaw + '</label><select class="jobapp_field_type" name="jobapp_' + fieldName + '[type]">' + fieldTypeHtml + '</select><input type="text" class="' + fieldName + ' jobapp_field_options" name="jobapp_' + fieldName + '[options]" value="' + fieldOptions + '" /> &nbsp; <div class="button removeField">Delete</div></li>');
                        $('.' + fieldName + ' .' + fieldType).attr('selected', 'selected');
                        $('#jobapp_name').val('');
                        $('#jobapp_field_type').val('text');
                        $('#jobapp_field_options').val('');
                        $('#jobapp_field_options').hide();
                    }
                }
                else {
                    alert("Please fill out field name");
                    $('#jobapp_name').focus(); //Keep focus on this input
                }

            });

            /* Job Application Field Type change (added) */
            $('#app_form_fields').on('change', 'li .jobapp_field_type', function () {

                var fieldType = $(this).val();

                if (!(fieldType == 'text' || fieldType == 'date' || fieldType == 'text_area')) {
                    $(this).next().show();
                }
                else {
                    $(this).next().hide();
                }
            });


            /*Add Job Feature*/
            $('#addFeature').click(function () {
                var fieldNameRaw = $('#jobfeature_name').val(); // Get Raw value.
                var fieldNameRaw = fieldNameRaw.trim();    // Remove White Spaces from both ends.
                var fieldName = fieldNameRaw.split(' ').join('_').toLowerCase(); //Replace white space with _.

                var fieldVal = $('#jobfeature_value').val();
                var fieldVal = fieldVal.trim();

                if (fieldName != '' && fieldVal != '') {
                    $('#job_features').append('<li class="' + fieldName + '"><label for="' + fieldName + '">' + fieldNameRaw + '</label> <input type="text" name="jobfeature_' + fieldName + '" value="' + fieldVal + '" > &nbsp; <div class="button removeField">Delete</div></li>');
                    $('#jobfeature_name').val(""); //Reset Field value.
                    $('#jobfeature_value').val(""); //Reset Field value.
                }
                else {
                    alert("Please fill out job feature");
                    $('#jobfeature_name').focus(); //Keep focus on this input
                }
            });
            /*Remove Job app or job Feature Fields*/
            $('.jobpost_fields').on('click', 'li .removeField', function () {
                $(this).parent('li').remove();
            });
        });
    </script>
    <?php
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function job_board_save_meta_boxes ( $post_id )
{

    /*
     * We need to verify this came from our screen and with proper authorization,
     * because the save_post action can be triggered at other times.
     */

    // Check if our nonce is set.
    if ( !isset ( $_POST[ 'jobpost_meta_box_nonce' ] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( !wp_verify_nonce ( $_POST[ 'jobpost_meta_box_nonce' ], 'myplugin_jobpost_meta_awesome_box' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined ( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset ( $_POST[ 'post_type' ] ) && 'page' == $_POST[ 'post_type' ] ) {

        if ( !current_user_can ( 'edit_page', $post_id ) ) {
            return;
        }
    } else {

        if ( !current_user_can ( 'edit_post', $post_id ) ) {
            return;
        }
    }

    /* OK, it's safe for us to save the data now. */

    //Delete fields.
    $old_keys = get_post_custom_keys ( $post_id );
    $new_keys = array_keys ( $_POST );
    $removed_keys = array_diff ( $old_keys, $new_keys ); //List of removed meta keys.
    foreach ( $removed_keys as $key => $val ):
        if ( substr ( $val, 0, 3 ) == 'job' )
            delete_post_meta ( $post_id, $val ); //Remove meta from the db.
    endforeach;

    // Add new value.
    foreach ( $_POST as $key => $val ):
        // Make sure that it is set.
        if ( substr ( $key, 0, 11 ) == 'jobfeature_' and isset ( $val ) ) {
            // Sanitize user input.
            $my_data = sanitize_text_field ( $val );
            update_post_meta ( $post_id, $key, $my_data ); // Add new value.
        }

        // Make sure that it is set.
        elseif ( substr ( $key, 0, 7 ) == 'jobapp_' and isset ( $val ) ) {
            $my_data = serialize ( $_POST[ $key ] );
            update_post_meta ( $post_id, $key, $my_data ); // Add new value.
        }
        // Update the meta field in the database.
    endforeach;
}

add_action ( 'save_post_jobpost', 'job_board_save_meta_boxes' );
