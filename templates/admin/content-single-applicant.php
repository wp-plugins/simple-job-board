<?php
/*
 * It creates a detail page for an application in the WP dashbaord for the administrator.
 */

function jobpost_applicants_detail_page_content ()
{
    global $post;
    if ( !empty ( $post ) and $post->post_type == 'jobpost_applicants' ):
        $keys = get_post_custom_keys ( $post->ID );
        ?>
        <div class="wrap"><div id="icon-tools" class="icon32"></div>
            <h3>
                <?php
                if ( in_array ( 'jobapp_name', $keys ) ):
                    echo get_post_meta ( $post->ID, 'jobapp_name', true );
                endif;
                if ( in_array ( 'resume', $keys ) ):
                    ?>
                    &nbsp; &nbsp; <small><a href="<?php echo get_post_meta ( $post->ID, 'resume', true ); ?>" target="_blank" >Resume</a></small>
                <?php endif; ?>

            </h3>
            <table class="widefat striped">
                <?php
                foreach ( $keys as $key ):
                    if ( substr ( $key, 0, 7 ) == 'jobapp_' ) {
                        echo '<tr><td>' . str_replace ( '_', ' ', substr ( $key, 7 ) ) . '</td><td>' . get_post_meta ( $post->ID, $key, true ) . '</td></tr>';
                    }
                endforeach;
                ?>
            </table>
        </div>
        <h2>Application Notes</h2>
        <?php
    endif;
}

add_action ( 'edit_form_after_title', 'jobpost_applicants_detail_page_content' );
