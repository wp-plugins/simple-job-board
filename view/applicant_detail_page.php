<?php

/* 
 * It creates a detail page for an application in the WP dashbaord for the administrator.
 */
function jobpost_applicants_detail_page() {
    $parent_slug='edit.php?post_type=jobpost';
    $page_title='New page Title';
    //$menu_title='Custom Settings';
    $capability='edit_posts';
    $menu_slug='applicant_detail';
    $function='applicants_detail_page_content';

    add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
}
//add_action('admin_menu', 'jobpost_applicants_detail_page');
add_action('edit_form_after_title', 'jobpost_applicants_detail_page_content');

function jobpost_applicants_detail_page_content(){
    global $post;
    if(!empty($post) and $post->post_type == 'jobpost_applicants'):
        $keys =  get_post_custom_keys($post->ID);
        ?>
        <div class="wrap"><div id="icon-tools" class="icon32"></div>
            <h3>
            <?php echo get_post_meta($post->ID, $keys[0], true); 
                if(in_array('resume', $keys)):
            ?>
                &nbsp; &nbsp; <small><a href="<?php echo get_post_meta($post->ID, 'resume', true); ?>" target="_blank" >Resume</a></small>
            <?php endif; ?>

            </h3>
            <table class="widefat striped">
            <?php
                foreach($keys as $key):
                    if(substr($key, 0, 7) == 'jobapp_'){ echo '<tr><td>'.  str_replace('_',' ',substr($key,7)).'</td><td>'.get_post_meta($post->ID, $key, true).'</td></tr>'; }
                endforeach; 
            ?>
            </table>
        </div>
        <h2>Application Notes</h2>
    <?php
    endif;
}
?>