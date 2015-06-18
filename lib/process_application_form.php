<?php
/* 
 * This library adds functionality to save application data into database. 
 */
function process_jobpost_form(){
    
 
    $nonce=$_POST['wp_nonce'];
    if(!wp_verify_nonce($nonce, 'the_best_jobpost_security_nonce')) die('Not Working');
    
    /*Initialixing Variables*/
    $error = null;
    $error_assignment = null;
    
/*
 * This part can be used for validation, 
 * A validation inputs can be taken from the admin panel, 
 * when application fields are being added to a particular job post
 */
/*    
    if( strlen($_POST['applicant_name'])<5 ) $error='Name is too short.';
    if( filter_var($email, FILTER_VALIDATE_EMAIL) === false ) $error='Invalid email address.';
    if( strlen($_POST['applicant_phone'])<7 ) $error = 'Invalid Phone Number.';
    if( strlen($_POST['applicant_address'])<5 ) $error = 'Address is too short.';
    if( strlen($_POST['applicant_experience'])<1 ) $error = 'Please enter work experience.';
*/  


    if (strlen($_FILES['applicant_resume']['name']) > 3) {
        $uploadfiles = $_FILES['applicant_resume'];
        if (is_array($uploadfiles)) {
                $upload_dir = wp_upload_dir();
                $assignment_upload_size = 200;
                $time = (!empty($_SERVER['REQUEST_TIME'])) ? $_SERVER['REQUEST_TIME'] : (time() + (get_option('gmt_offset') * 3600)); // Fallback of now

                $post_type = 'jobpost';
                $date = explode(" ", date('Y m d H i s', $time));
                $timestamp = strtotime(date('Y m d H i s'));
                if($post_type)
                {
                          $upload_dir = array(
                                'path' => WP_CONTENT_DIR . '/uploads/' . $post_type . '/' . $date[0],
                                'url' => WP_CONTENT_URL . '/uploads/' . $post_type . '/' . $date[0],
                                'subdir' => '',
                                'basedir' => WP_CONTENT_DIR . '/uploads',
                                'baseurl' => WP_CONTENT_URL . '/uploads',
                                'error' => false,
                          );
                 }
                if(!is_dir($upload_dir['path'])){
                   wp_mkdir_p($upload_dir['path']);
                }
                $var_cp_assigment_type= 'png';
                $uploadfiles = array(
                        'name'     => $_FILES['applicant_resume']['name'],
                        'type'     => $_FILES['applicant_resume']['type'],
                        'tmp_name' => $_FILES['applicant_resume']['tmp_name'],
                        'error'    => $_FILES['applicant_resume']['error'],
                        'size'     => $_FILES['applicant_resume']['size']
                );

          // look only for uploded files
          if ($uploadfiles['error'] == 0) {
                $filetmp = $uploadfiles['tmp_name'];
                $filename = $uploadfiles['name'];
                $filesize = $uploadfiles['size'];
                $max_upload_size = $assignment_upload_size*1048576; //Multiply by KBs
                if($max_upload_size<$filesize){
                        $assignment_error[] = 'Maximum upload File size allowed '.$assignment_upload_size.'MB';
                        $error_assignment = 1;
                }
                        $file_type_match = 0;
                        $var_cp_assigment_type_array = array();
                         if($var_cp_assigment_type){
                                 $var_cp_assigment_type_array = explode(',',$var_cp_assigment_type);
                         }
    //                         if(in_array($uploadfiles['type'], $var_cp_assigment_type_array)){
    //                                $file_type_match = 1;
    //                         }
                         /**
                         * Check File Size
                         */
    //                        if($file_type_match <> 1){
    //                                $assignment_error[] = 'Please upload file with extenstion '.$var_cp_assigment_type;
    //                                $error_assignment = 1;
    //                        }
                        // get file info
                        // @fixme: wp checks the file extension....
                        $filetype = wp_check_filetype( basename( $filename ), null );
                        $filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
                        $filename = $filetitle . $timestamp . '.' . $filetype['ext'];
                        /**
                         * Check if the filename already exist in the directory and rename the
                         * file if necessary
                         */
                        $i = 0;
                        while ( file_exists( $upload_dir['path'] .'/' . $filename ) ) {
                          $filename = $filetitle . $timestamp . '_' . $i . '.' . $filetype['ext'];
                          $i++;
                        }
                        $filedest = $upload_dir['path'] . '/' . $filename;
                        /**
                         * Check write permissions
                         */
                        if ( !is_writeable( $upload_dir['path'] ) ) {
                          $assignment_error[] = 'Unable to write to directory %s. Is this directory writable by the server?';
                          $error_assignment = 1;
                        }
                        /**
                         * Save temporary file to uploads dir
                         */
                        if($error_assignment <> 1){
                                if ( !@move_uploaded_file($filetmp, $filedest) ){
                                  $assignment_error[] = 'Error, the file $filetmp could not moved to : $filedest';
                                  $error_assignment = 1;
                                }
                                $newupload = $upload_dir['url'].'/'.$filename;
                                $uploadpath = $upload_dir['path'].'/'.$filename;
                        }
                  }
          }
    }
        if($error_assignment == 1){
                $errors = '<div style="padding:15px;background-color: #f2dede;">';
                foreach($assignment_error as $error_value){
                        $errors .= '<p>'.esc_html__($error_value, 'EDULMS').'</p>';
                }
                $errors .= '</div>';
                $json['message'] = $errors;
                $json['error'] = 1;
                echo json_encode( $json );
                die();
        }

    $args=  array(
        'post_type'     =>'jobpost_applicants',
        'post_content'  =>'',
        'post_parent'    =>$_POST['job_id'],
        'post_title'    =>get_the_title($_POST['job_id']),
        'post_status'   =>'publish',
    );
    $pid=wp_insert_post($args);

    foreach($_POST as $key => $val):
        if(substr($key,0,7) == 'jobapp_') add_post_meta($pid, $key, $val);
        if(!empty($newupload)) add_post_meta($pid, 'resume', $newupload);
    endforeach;

    if($pid>0) $response = json_encode( array( 'success' => true ));    // generate the response.
    else $response = json_encode( array( 'success' => false ));    // generate the response.

    if($error) $response = json_encode( array( 'success' => false, 'error' => $error ));    // generate the response with error message.

    // response output
    header( "Content-Type: application/json" );
    echo $response;
 
    exit;
}
add_action( 'wp_ajax_nopriv_process_jobpost_form', 'process_jobpost_form' );
add_action( 'wp_ajax_process_jobpost_form', 'process_jobpost_form' );

?>