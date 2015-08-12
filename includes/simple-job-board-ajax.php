<?php

// Entertain Applicant Request From Job Apply Form
function process_applicant_form ()
{
    $nonce = $_POST[ 'wp_nonce' ];
    if ( !wp_verify_nonce ( $nonce, 'the_best_jobpost_security_nonce' ) )
        die ( 'Not Working' );

    /* Initialixing Variables */
    $error = NULL;
    $error_assignment = NULL;

    if ( strlen ( $_FILES[ 'applicant_resume' ][ 'name' ] ) > 3 ) {
        $uploadfiles = $_FILES[ 'applicant_resume' ];
        if ( is_array ( $uploadfiles ) ) {
            $upload_dir = wp_upload_dir ();
            $assignment_upload_size = 200;
            $time = (!empty ( $_SERVER[ 'REQUEST_TIME' ] )) ? $_SERVER[ 'REQUEST_TIME' ] : (time () + (get_option ( 'gmt_offset' ) * 3600)); // Fallback of now

            $post_type = 'jobpost';
            $date = explode ( " ", date ( 'Y m d H i s', $time ) );
            $timestamp = strtotime ( date ( 'Y m d H i s' ) );
            if ( $post_type ) {
                $upload_dir = array (
                    'path' => WP_CONTENT_DIR . '/uploads/' . $post_type . '/' . $date[ 0 ],
                    'url' => WP_CONTENT_URL . '/uploads/' . $post_type . '/' . $date[ 0 ],
                    'subdir' => '',
                    'basedir' => WP_CONTENT_DIR . '/uploads',
                    'baseurl' => WP_CONTENT_URL . '/uploads',
                    'error' => false,
                );
            }
            if ( !is_dir ( $upload_dir[ 'path' ] ) ) {
                wp_mkdir_p ( $upload_dir[ 'path' ] );
            }
            $var_cp_assigment_type = 'png';
            $uploadfiles = array (
                'name' => $_FILES[ 'applicant_resume' ][ 'name' ],
                'type' => $_FILES[ 'applicant_resume' ][ 'type' ],
                'tmp_name' => $_FILES[ 'applicant_resume' ][ 'tmp_name' ],
                'error' => $_FILES[ 'applicant_resume' ][ 'error' ],
                'size' => $_FILES[ 'applicant_resume' ][ 'size' ]
            );

            // look only for uploded files
            if ( $uploadfiles[ 'error' ] == 0 ) {
                $filetmp = $uploadfiles[ 'tmp_name' ];
                $filename = $uploadfiles[ 'name' ];
                $filesize = $uploadfiles[ 'size' ];
                $max_upload_size = $assignment_upload_size * 1048576; //Multiply by KBs
                if ( $max_upload_size < $filesize ) {
                    $assignment_error[] = 'Maximum upload File size allowed ' . $assignment_upload_size . 'MB';
                    $error_assignment = 1;
                }
                $file_type_match = 0;
                $var_cp_assigment_type_array = array ();
                if ( $var_cp_assigment_type ) {
                    $var_cp_assigment_type_array = explode ( ',', $var_cp_assigment_type );
                }

                // get file info
                // @fixme: wp checks the file extension....
                $filetype = wp_check_filetype ( basename ( $filename ), NULL );
                $filetitle = preg_replace ( '/\.[^.]+$/', '', basename ( $filename ) );
                $filename = $filetitle . $timestamp . '.' . $filetype[ 'ext' ];

                /**
                 * Check if the filename already exist in the directory & rename
                 * the file if necessary
                 */
                $i = 0;
                while ( file_exists ( $upload_dir[ 'path' ] . '/' . $filename ) ) {
                    $filename = $filetitle . $timestamp . '_' . $i . '.' . $filetype[ 'ext' ];
                    $i++;
                }
                $filedest = $upload_dir[ 'path' ] . '/' . $filename;

                // Check write permissions
                if ( !is_writeable ( $upload_dir[ 'path' ] ) ) {
                    $assignment_error[] = 'Unable to write to directory %s. Is this directory writable by the server?';
                    $error_assignment = 1;
                }

                //Save Temporary File to Uploads Dir
                if ( $error_assignment <> 1 ) {
                    if ( !@move_uploaded_file ( $filetmp, $filedest ) ) {
                        $assignment_error[] = 'Error, the file $filetmp could not moved to : $filedest';
                        $error_assignment = 1;
                    }
                    $url = $upload_dir[ 'url' ];
                    $path = $upload_dir[ 'path' ];
                    $newupload = $upload_dir[ 'url' ] . '/' . $filename;
                    $uploadpath = $upload_dir[ 'path' ] . '/' . $filename;
                }
            }
        }
    }
    if ( $error_assignment == 1 ) {
        $errors = '<div style="padding:15px;background-color: #f2dede;">';
        foreach ( $assignment_error as $error_value ) {
            $errors .= '<p>' . esc_html__ ( $error_value, 'EDULMS' ) . '</p>';
        }
        $errors .= '</div>';
        $json[ 'message' ] = $errors;
        $json[ 'error' ] = 1;
        echo json_encode ( $json );
        die ();
    }

    $args = array (
        'post_type' => 'jobpost_applicants',
        'post_content' => '',
        'post_parent' => $_POST[ 'job_id' ],
        'post_title' => get_the_title ( $_POST[ 'job_id' ] ),
        'post_status' => 'publish',
    );
    $pid = wp_insert_post ( $args );
    $resume_name = $pid . '_' . $filename;
    $resume_url = $url . '/' . $resume_name;
    $resume_path = $path . '/' . $resume_name;
    rename ( $uploadpath, $resume_path );

    foreach ( $_POST as $key => $val ):

        if ( substr ( $key, 0, 7 ) == 'jobapp_' ):
            add_post_meta ( $pid, $key, $val );
        endif;
        if ( !empty ( $newupload ) ) {
            add_post_meta ( $pid, 'resume', $resume_url );
        }

    endforeach;
    add_post_meta ( $pid, 'resume_path', $resume_path );

    if ( $pid > 0 )
        $response = json_encode ( array ( 'success' => TRUE ) );    // generate the response.
    else
        $response = json_encode ( array ( 'success' => FALSE ) );    // generate the response.

    if ( $error )
        $response = json_encode ( array ( 'success' => FALSE, 'error' => $error ) );    // generate the response with error message.
       
// response output
    header ( "Content-Type: application/json" );
    echo $response;

    // Admin Notification  
    if ( 'yes' === get_option ( 'job_board_admin_notification' ) )
        admin_notification ( $pid );
    //  HR Notification
    if ( ('yes' === get_option ( 'job_board_hr_notification' )) && ('' != get_option ( 'settings_hr_email' )) )
        hr_notification ( $pid );

    // Applicant Notification
    if ( 'yes' === get_option ( 'job_board_applicant_notification' ) )
        applicant_notification ( $pid );
    exit ();
}

// Hook - Entertain Applicant Request From Job Apply Form
add_action ( 'wp_ajax_nopriv_process_applicant_form', 'process_applicant_form' );
add_action ( 'wp_ajax_process_applicant_form', 'process_applicant_form' );