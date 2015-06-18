<?php 
function jobpost_form(){
    ob_start();
    ?>
   <!-- <form class="jobpost_form"> -->
    <form class="jobpost_form" name="c-assignments-form" id="cs-assignments-form" enctype="multipart/form-data">
        <h3>Apply Online</h3>
        <div class="form-group"><label for="applicant_name">Full Name </label><input type="text" name="applicant_name" class="form-control" id="applicant_name" required></div>
        <div class="form-group"><label for="applicant_gender">Gender </label> <br /><input type="radio" name="applicant_gender" value="m" class="" id="applicant_gender" checked>Male <br/><input type="radio" name="applicant_gender" value="f" class="">Female </div>
        <div class="form-group"><label for="applicant_dob">Date of Birth </label> <input type="date" name="applicant_dob" class="datepicker form-control" id="applicant_dob" required placeholder="DD-MM-YYYY"></div>
        <div class="form-group"><label for="applicant_email">Email </label><input type="text" name="applicant_email" class="form-control" id="applicant_email" required></div>
        <div class="form-group"><label for="applicant_phone">Phone </label><input type="text" name="applicant_phone" class="form-control" id="applicant_phone" required></div>
        <div class="form-group"><label for="applicant_address">Address </label><textarea name="applicant_address" class="form-control" id="applicant_address" required></textarea></div>
        <div class="form-group"><label for="applicant_experience">Experience </label><input type="text" name="applicant_experience" class="form-control" id="applicant_experience" required></div>
        <div class="form-group"><label for="applicant_description">Describe yourself </label><textarea name="applicant_description" class="form-control" id="applicant_description" required></textarea></div>
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
            $metas.= '<tr><td>Salary</td><td>'.  get_post_meta($post->ID,'jobpost_salary',true).' </td></tr>';
            $metas.= '<tr><td>Functional Area</td><td>'.  get_post_meta($post->ID,'jobpost_department',true).' </td></tr>';
            $metas.= '<tr><td>Location</td><td>'.  get_post_meta($post->ID,'jobpost_location',true).' </td></tr>';
            $metas.= '<tr><td>Education Required</td><td>'.  get_post_meta($post->ID,'jobpost_education',true).' </td></tr>';
            $metas.= '<tr><td>Experience  Required</td><td>'.  get_post_meta($post->ID,'jobpost_experience',true).' </td></tr>';
            $metas.= '<tr><td>Number of Postions</td><td>'.  get_post_meta($post->ID,'jobpost_positions',true).' </td></tr>';
        $metas.='</table>';
        $content=$content.$metas.jobpost_form();
    endif;
  return $content;
}
add_filter( 'the_content', 'filter_function_name');
?>