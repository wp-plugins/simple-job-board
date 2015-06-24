<?php

/**
 * Adds Meta boxes to the main column on the jobpost edit screens.
 */
function jobpost_add_meta_box() {

    add_meta_box(
            'jobpost_metas',
            __( 'Job Features', 'wpquantum' ),
            'jobpost_meta_box_callback',
            'jobpost'
    );
    
    add_meta_box(
            'jobpost_application_fields',
            __( 'Application Form Fields', 'wpquantum' ),
            'jobpost_application_form_callback',
            'jobpost'
    );
}
add_action( 'add_meta_boxes', 'jobpost_add_meta_box' );

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function jobpost_meta_box_callback( $post ) {
    global $jobfields;

    // Add a nonce field so we can check for it later.
	wp_nonce_field( 'myplugin_jobpost_meta_awesome_box', 'jobpost_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
?>
<div class="job_features jobpost_fields">
    <ol id="job_features">
<?php
$keys = get_post_custom_keys( $post->ID);
if($keys != NULL):
    foreach($keys as $key):
        if(substr($key, 0, 11)=='jobfeature_'){
            $val=get_post_meta($post->ID, $key, TRUE);
            echo '<li><label for="'.$key.'">';
            _e( str_replace('_',' ',substr($key,11)), 'wpquantum' );
            echo '</label> ';
            echo '<input type="text" id="'.$key.'" name="'.$key.'" value="' . esc_attr( $val ) . '" /> &nbsp; <div class="button removeField">Delete</div></li>';
        }
    endforeach;
endif;
?>
    </ol>
</div>
<div class="clearfix clear"></div>
<table id="jobfeatures_form" class="alignleft">
<thead>
    <tr>
        <th class="left"><label for="jobFeature">Feature</label></th>
        <th><label for="jobFeatureVal">Value</label></th>
    </tr>
</thead>
<tbody>
    <tr>
        <td class="left" id="jobFeature">
            <input type="text" id="jobfeature_name" />
        </td><td>
            <input type="text" id="jobfeature_value" />
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div class=""><div class="button" id="addFeature">Add Field</div></div>
        </td>
    </tr>
</tbody>
</table>
<div class="clearfix clear"></div>
<?php 
}

function jobpost_application_form_callback( $post ) {
    global $jobfields;
    // Add a nonce field so we can check for it later.
	wp_nonce_field( 'myplugin_jobpost_meta_awesome_box', 'jobpost_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */

?>
<style>
    .app_form_fields{background: #F9F9F9}
    .app_form_fields .jobapp_group{padding-bottom: 20px;}
    
</style>
<div class="app_form_fields jobpost_fields">
    <ol id="app_form_fields">
        <?php
            $field_types = array('text'=>'Text Field', 'text_area'=>'Text Area', 'date'=>'Date', 'checkbox'=>'Check Box', 'dropdown'=>'Drop Down', 'radio'=> 'Radio');
            
            $keys=get_post_custom_keys( $post->ID);
            if($keys != NULL):
                foreach($keys as $key):
                    if(substr($key, 0, 7)=='jobapp_'):
                    
                        $val=get_post_meta($post->ID, $key, TRUE);
                        $val=  unserialize($val);
                        $fields = NULL;
                        foreach($field_types as $field_key => $field_val){
                            if($val['type']==$field_key) $fields .= '<option value="'.$field_key.'" selected>'.$field_val.'</option>';
                            else $fields .= '<option value="'.$field_key.'" >'.$field_val.'</option>';
                        }                   
                        //if($key.'[type]'=='text'){
                            echo '<li class="'.$key.'"><label for="">'.str_replace('_',' ',substr($key,7)).'</label><select class="jobapp_field_type" name="'.$key.'[type]">'.$fields.'</select>';
                            if(!($val['type']=='text' or $val['type']=='date' or $val['type']=='text_area' )):
                                echo '<input type="text" name="'.$key.'[options]" value="'.$val['options'].'" placeholder="Option1, option2, option3" />';
                            else:
                                echo '<input type="text" name="'.$key.'[options]" placeholder="Option1, option2, option3" style="display:none;"  />';
                            endif;
                            echo ' &nbsp; <div class="button removeField">Delete</div></li>';
                        //}
                    endif;
                endforeach;
            endif;
         ?>
        
    </ol>
</div>
<div class="clearfix clear"></div>
<table id="jobapp_form_fields" class="alignleft">
<thead>
    <tr>
        <th class="left"><label for="metakeyselect">Field</label></th>
        <th><label for="metavalue">Type</label></th>
    </tr>
</thead>
<tbody>
    <tr>
        <td class="left" id="newmetaleft">
            <input type="text" id="jobapp_name" />
        </td><td>
            <select id="jobapp_field_type">
                <?php
                    foreach($field_types as $key => $val):
                        echo '<option value="'.$key.'" class="'.$key.'">'.$val.'</option>';
                    endforeach;
                ?>
            </select>
        </td>
    </tr>

    <tr>
        <td colspan="2" ><input id="jobapp_field_options" class="jobapp_field_type" type="text" style="display: none;" placeholder="Option1, Option2, Option3" ></td>
    </tr>
    <tr>
        <td colspan="2">
            <div class=""><div class="button" id="addField">Add Field</div></div>
        </td>
    </tr>
</tbody>
</table>
<div class="clearfix clear"></div>
<script>
    jQuery('document').ready(function($){
        /*Job Application Field Type change*/
        $('#jobapp_field_type').change(function(){
           var fieldType=$(this).val();

           if(!(fieldType == 'text' || fieldType == 'date' || fieldType == 'text_area')){
               $('#jobapp_field_options').show();
           }
           else{
               $('#jobapp_field_options').hide();
               $('#jobapp_field_options').val('');
           }
        });
        
        /*Add Application Field (Group Fields)*/
        $('#addField').click(function(){
            var fieldNameRaw=$('#jobapp_name').val(); // Get Raw value.
            var fieldNameRaw = fieldNameRaw.trim();    // Remove White Spaces from both ends.
            var fieldName = fieldNameRaw.replace(" ", "_"); //Replace white space with _.
            var fieldType = $('#jobapp_field_type').val();
            var fieldOptions = $('#jobapp_field_options').val();
            
            
            var fieldTypeHtml = $('#jobapp_field_type').html();
            if(fieldName != ''){
                if(fieldType=='text' || fieldType=='date' || fieldType == 'text_area'){
                    $('#app_form_fields').append('<li class="'+fieldName+'"><label for="'+fieldName+'">'+fieldNameRaw+'</label><select class="jobapp_field_type" name="jobapp_'+fieldName+'[type]">'+fieldTypeHtml+'</select> &nbsp; <div class="button removeField">Delete</div></li>');
                    $('.'+fieldName+' .'+fieldType).attr('selected','selected');
                    $('#jobapp_name').val('');
                    $('#jobapp_field_type').val('text');
                }
                else {
                    $('#app_form_fields').append('<li class="'+fieldName+'"><label for="'+fieldName+'">'+fieldNameRaw+'</label><select class="jobapp_field_type" name="jobapp_'+fieldName+'[type]">'+fieldTypeHtml+'</select><input type="text" class="'+fieldName+' jobapp_field_options" name="jobapp_'+fieldName+'[options]" value="'+fieldOptions+'" /> &nbsp; <div class="button removeField">Delete</div></li>');
                    $('.'+fieldName+' .'+fieldType).attr('selected','selected');
                    $('#jobapp_name').val('');
                    $('#jobapp_field_type').val('text');
                    $('#jobapp_field_options').val('');
                    $('#jobapp_field_options').hide();
                }
            }
            
        });

        /* Job Application Field Type change (added) */
        $('#app_form_fields').on('change', 'li .jobapp_field_type',function(){
          
            var fieldType=$(this).val();

           if(!(fieldType == 'text' || fieldType == 'date' || fieldType == 'text_area')){
               $(this).next().show();
           }
           else{
               $(this).next().hide();
           }
        }); 


        /*Add Job Feature*/
        $('#addFeature').click(function(){
            var fieldNameRaw=$('#jobfeature_name').val(); // Get Raw value.
            var fieldNameRaw = fieldNameRaw.trim();    // Remove White Spaces from both ends.
            var fieldName = fieldNameRaw.replace(" ", "_"); //Replace white space with _.
            
            var fieldVal = $('#jobfeature_value').val();
            var fieldVal = fieldVal.trim();
            
            if(fieldName != '' && fieldVal!=''){
                $('#job_features').append('<li class="'+fieldName+'"><label for="'+fieldName+'">'+fieldNameRaw+'</label> <input type="text" name="jobfeature_'+fieldName+'" value="'+fieldVal+'" > &nbsp; <div class="button removeField">Delete</div></li>');
                $('#jobfeature_name').val(""); //Reset Field value.
                $('#jobfeature_value').val(""); //Reset Field value.
            }
        });
        /*Remove Job app or job Feature Fields*/
        $('.jobpost_fields').on('click', 'li .removeField',function(){
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
function jobpost_save_meta_box_data( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['jobpost_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['jobpost_meta_box_nonce'], 'myplugin_jobpost_meta_awesome_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, it's safe for us to save the data now. */
        
        //Delete fields.
        $old_keys = get_post_custom_keys($post_id);
        $new_keys = array_keys($_POST);
        $removed_keys = array_diff($old_keys, $new_keys); //List of removed meta keys.
        foreach($removed_keys as $key => $val):
            if(substr($val, 0, 3) == 'job') delete_post_meta($post_id, $val); //Remove meta from the db.
        endforeach;

        // Add new value.
        foreach ($_POST as $key => $val):
            // Make sure that it is set.
            if ( substr($key, 0, 11)=='jobfeature_' and isset( $val ) ) {
                // Sanitize user input.
                $my_data = sanitize_text_field( $val );
                update_post_meta( $post_id, $key,  $my_data); // Add new value.
            }
            
            // Make sure that it is set.
            elseif ( substr($key, 0, 7)=='jobapp_' and isset( $val ) ) {
                $my_data = serialize($_POST[$key]);
                update_post_meta( $post_id, $key,  $my_data); // Add new value.
            }
                // Update the meta field in the database.
        endforeach;
}
add_action( 'save_post_jobpost', 'jobpost_save_meta_box_data' );