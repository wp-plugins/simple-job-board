<?php 
function jobpost_form(){
    ob_start();
    ?>
   <!-- <form class="jobpost_form"> -->
    <form class="jobpost_form" name="c-assignments-form" id="cs-assignments-form" enctype="multipart/form-data">
        <h3>Apply Online</h3>
        <?php 
            $field_types=array('text'=>'Text', 'checkbox'=>'Check Box', 'dropdown'=>'Drop Down', 'radio'=> 'Radio');

            $keys=get_post_custom_keys(get_the_ID());
            if($keys != NULL):
                foreach($keys as $key):
                    if(substr($key, 0, 7)=='jobapp_'):
                        $val=get_post_meta(get_the_ID(), $key, TRUE);
                        $val=  unserialize($val);
                        switch ($val['type']){
                            case 'text': echo '<div class="form-group"><label for="'.$key.'">'.str_replace('_',' ',substr($key,7)).'</label><input type="text" name="'.$key.'" class="form-control" id="'.$key.'" required></div>';
                                break;
                            
                            case 'text_area':
                                echo '<div class="form-group"><label for="'.$key.'">'.str_replace('_',' ',substr($key,7)).'</label><textarea name="'.$key.'" class="form-control" id="'.$key.'" required></textarea></div>';
                                break;
                            
                            case 'date': echo '<div class="form-group"><label for="'.$key.'">'.str_replace('_',' ',substr($key,7)).'</label><input type="text" name="'.$key.'" class="form-control datepicker" id="'.$key.'" required></div>';
                                break;
                            
                            case 'radio': echo '<div class="form-group"><label for="'.$key.'">'.str_replace('_',' ',substr($key,7)).'</label><div id="'.$key.'" >';
                                
                                $options=explode(',', $val['options']);
                                $i=0;
                                foreach ($options as $option) {
                                    echo '<input type="radio" name="'.$key.'" class="" id="'.$key.'" value="'.$option.'"  '.is_checked($i).'>'.$option.' &nbsp; &nbsp; ';
                                    $i++;
                                }
                                echo '</div></div>';
                                break;
                            
                            case 'dropdown': echo '<div class="form-group"><label for="'.$key.'">'.str_replace('_',' ',substr($key,7)).'</label><div id="'.$key.'" ><select name="'.$key.'" id="'.$key.'" required>';
                                $options = explode(',', $val['options']);
                                foreach ($options as $option) {
                                    echo '<option class="" value="'.$option.'" >'.$option.' </option>';
                                }
                                echo '</select></div></div>';
                            
                            case 'checkbox' : echo '<div class="form-group"><label for="'.$key.'">'.str_replace('_',' ',substr($key,7)).'</label><div id="'.$key.'" >';
                                $options=explode(',', $val['options']);
                                $i=0;
                                foreach ($options as $option) {
                                    echo '<input type="checkbox" name="'.$key.'" class="" id="'.$key.'" value="'.$option.'"  '.is_checked($i).'>'.$option.' &nbsp; &nbsp; ';
                                    $i++;
                                }
                                echo '</div></div>';
                                break;
                        }
                    endif;
                endforeach;
            endif;
        ?>
        <div class="form-group"><label for="applicant_resume">Attach Resume </label><input type="file" name="applicant_resume" class="" id="applicant_resume"></div>
        <input type="hidden" name="job_id" value="<?php the_ID(); ?>" >
        <input type="hidden" name="action" value="process_jobpost_form" >
        <input type="hidden" name="wp_nonce" value="<?php echo wp_create_nonce( 'the_best_jobpost_security_nonce' ) ?>" >
        <input type="submit" value="Submit" id="jobpost_submit_button">
    </form><div id="jobpost_form_status"></div>
    <script>
        jQuery(document).ready(function() {
            jQuery('.datepicker').datepicker({
                dateFormat : 'dd-mm-yy',
                changeMonth: true,
                changeYear: true
            });
        });
    </script>
<?php
    return ob_get_clean();
}

function filter_function_name($content) {
    global $post;
    if(is_single() and $post->post_type=='jobpost'):
        $metas='<h3>Job Features</h3><table class="jobpost_features">';
        $keys = get_post_custom_keys(get_the_ID());
        if($keys != NULL):
            foreach($keys as $key):
                if(substr($key, 0, 11)=='jobfeature_'){
                    $val=get_post_meta($post->ID, $key, TRUE);
                    $metas.= '<tr><td>'.str_replace('_',' ',substr($key,11)).'</td><td>'.$val.' </td></tr>';
                }
            endforeach;
        endif;
        $metas.='</table>';
        $content=$content.$metas.jobpost_form();
    endif;
  return $content;
}
add_filter( 'the_content', 'filter_function_name');
?>