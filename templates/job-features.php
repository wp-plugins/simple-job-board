<?php

function job_board_features_list ( $content )
{
    global $post;
    
    if ( is_single () and $post->post_type == 'jobpost' ):
        $metas = '<h3>Job Features</h3><table class="jobpost_features">';
        $keys = get_post_custom_keys ( get_the_ID () );
        
        if ( $keys != NULL ):
            foreach ( $keys as $key ):
                if ( substr ( $key, 0, 11 ) == 'jobfeature_' ) {
                    $val = get_post_meta ( $post->ID, $key, TRUE );
                    if ( $val != '' )
                        $metas.= '<tr><td>' . ucwords ( str_replace ( '_', ' ', substr ( $key, 11 ) ) ) . '</td><td>' . $val . ' </td></tr>';
                }
            endforeach;
        endif;
        $job_category = wp_get_post_terms ( $post->ID, 'jobpost_category' );
        $job_type = wp_get_post_terms ( $post->ID, 'jobpost_job_type' );
        $job_location = wp_get_post_terms ( $post->ID, 'jobpost_location' );

        if ( NULL != $job_category ):
            $count = sizeof ( $job_category );
            $metas.= '<tr><td>Job Category</td><td>';
            foreach ( $job_category as $cat ) {
                $metas.=$cat->name;
                if ( $count > 1 ) {
                    $metas.='&nbsp, ';
                }
                $count--;
            }
            $metas.='</td></tr>';
        endif;
        
        if ( NULL != $job_type ):
            $count = sizeof ( $job_type );
            $metas.= '<tr><td>Job Type</td><td>';
            foreach ( $job_type as $type ) {
                $metas.=$type->name;
                if ( $count > 1 ) {
                    $metas.='&nbsp, ';
                }
                $count--;
            }
            $metas.='</td></tr>';
        endif;
        
        if ( NULL != $job_location ):
            $count = sizeof ( $job_location );
            $metas.= '<tr><td>Job Location</td><td>';
            foreach ( $job_location as $location ) {
                $metas.=$location->name;
                if ( $count > 1 ) {
                    $metas.='&nbsp, ';
                }
                $count--;
            }
            $metas.='</td></tr>';
        endif;

        $metas.='</table>';
        $content = $content . $metas . job_board_application_form ();
    endif;
    return $content;
}

add_filter ( 'the_content', 'job_board_features_list' );
