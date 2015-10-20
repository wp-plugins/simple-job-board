<?php
/**
 * Single view Job Fetures
 *
 * Hooked into single_job_listing_end priority 20
 *
 * @since  2.1.0
 */
global $post;

$metas = '<h3>'.__( 'Job Features' , 'simple-job-board' ).'</h3>';
    $metas .= '<table class="jobpost_features">';

$job_category = wp_get_post_terms ( $post->ID, 'jobpost_category' );

if ( NULL != $job_category ):
    $count = sizeof ( $job_category );
    $metas.= '<tr><td>'.__( 'Job Category' ,'simple-job-board' ).'</td><td>';
    foreach ( $job_category as $cat ) {
        $metas.=$cat->name;
        if ( $count > 1 ) {
            $metas.='&nbsp, ';
        }
        $count--;
    }
    $metas.='</td></tr>';
endif;
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

    $metas.='</table>';
    
echo $metas;