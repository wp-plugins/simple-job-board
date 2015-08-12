<?php
function job_board_application_form ()
{
    ob_start ();
    ?>
    <div id="apply">
        <form class="jobpost_form" name="c-assignments-form" id="cs-assignments-form" enctype="multipart/form-data">
            <h3>Apply Online</h3>
            <?php
            $field_types = array ( 'text' => 'Text', 'checkbox' => 'Check Box', 'dropdown' => 'Drop Down', 'radio' => 'Radio' );

            $keys = get_post_custom_keys ( get_the_ID () );
            if ( NULL != $keys ):
                foreach ( $keys as $key ):
                    if ( substr ( $key, 0, 7 ) == 'jobapp_' ):
                        $val = get_post_meta ( get_the_ID (), $key, TRUE );
                        $val = unserialize ( $val );
                        switch ( $val[ 'type' ] ) {
                            case 'text':
                                echo '<div class="form-group"><label for="' . $key . '">' . ucwords(str_replace ( '_', ' ', substr ( $key, 7 ) )) . '</label><input type="text" name="' . $key . '" class="form-control" id="' . $key . '" required></div>';
                            break;

                            case 'text_area':
                                echo '<div class="form-group"><label for="' . $key . '">' . ucwords(str_replace ( '_', ' ', substr ( $key, 7 ) )) . '</label><textarea name="' . $key . '" class="form-control" id="' . $key . '" required></textarea></div>';
                            break;

                            case 'date': echo '<div class="form-group"><label for="' . $key . '">' . ucwords(str_replace ( '_', ' ', substr ( $key, 7 ) )) . '</label><input type="text" name="' . $key . '" class="form-control datepicker" id="' . $key . '" required></div>';
                            break;

                            case 'radio':
                                if ( $val[ 'options' ] != '' ) {
                                    echo '<div class="form-group"><label for="' . $key . '">' .ucwords( str_replace ( '_', ' ', substr ( $key, 7 ) )) . '</label><div id="' . $key . '" >';
                                    $options = explode ( ',', $val[ 'options' ] );
                                    $i = 0;
                                    foreach ( $options as $option ) {
                                        echo '<input type="radio" name="' . $key . '" class="" id="' . $key . '" value="' . $option . '"  ' . job_board_is_checked ( $i ) . '>' . $option . ' &nbsp; &nbsp; ';
                                        $i++;
                                    }
                                    echo '</div></div>';
                                }
                            break;

                            case 'dropdown':
                                if ( $val[ 'options' ] != '' ) {
                                    echo '<div class="form-group"><label for="' . $key . '">' . ucwords(str_replace ( '_', ' ', substr ( $key, 7 ) )) . '</label><div id="' . $key . '" ><select name="' . $key . '" id="' . $key . '" required>';
                                    $options = explode ( ',', $val[ 'options' ] );
                                    foreach ( $options as $option ) {
                                        echo '<option class="" value="' . $option . '" >' . $option . ' </option>';
                                    }
                                    echo '</select></div></div>';
                                }
                            break;

                            case 'checkbox' :
                                if ( $val[ 'options' ] != '' ) {
                                    echo '<div class="form-group"><label for="' . $key . '">' . ucwords(str_replace ( '_', ' ', substr ( $key, 7 ) )) . '</label><div id="' . $key . '" >';
                                    $options = explode ( ',', $val[ 'options' ] );
                                    $i = 0;
                                    foreach ( $options as $option ) {
                                        echo '<input type="checkbox" name="' . $key . '" class="" id="' . $key . '" value="' . $option . '"  ' . job_board_is_checked ( $i ) . '>' . $option . ' &nbsp; &nbsp; ';
                                        $i++;
                                    }
                                    echo '</div></div>';
                                }
                            break;
                        }
                    endif;
                endforeach;
            endif;
            ?>
            <div class="form-group"><label for="applicant_resume">Attach Resume </label>
                <input type="file" name="applicant_resume" class="" id="applicant_resume"><label id="file_error_message" style="color:red;"></label></div>
            <input type="hidden" name="job_id" value="<?php the_ID (); ?>" >
            <input type="hidden" name="action" value="process_applicant_form" >
            <input type="hidden" name="wp_nonce" value="<?php echo wp_create_nonce ( 'the_best_jobpost_security_nonce' ) ?>" >
            <input type="submit" value="Submit" id="jobpost_submit_button">
        </form></div>
    <div id="jobpost_form_status"></div>
    <?php
    return ob_get_clean ();
}