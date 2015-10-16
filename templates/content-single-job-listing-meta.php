<?php
/**
 * Single view Job Meta Box
 *
 * Hooked into single_job_listing_start priority 20
 *
 * @since  2.1.0
 */
global $post;

do_action( 'single_job_listing_meta_before' ); ?>
<ul class="meta">
    <?php do_action( 'single_job_listing_meta_start' ); ?>
    <li class="<?php echo sjb_get_the_job_type() ? sanitize_title( sjb_get_the_job_type()->slug ) : ''; ?>"><?php sjb_the_job_type(); ?></li>
    <li><?php sjb_the_job_location(); ?></li>
    <li><date><span class="glyphicon glyphicon-calendar"></span><?php printf( __( 'Posted %s ago', 'simple-job-board' ), human_time_diff( get_post_time( 'U' ), current_time( 'timestamp' ) ) ); ?></date></li>
    <?php do_action( 'single_job_listing_meta_end' ); ?>
</ul>
<?php do_action( 'single_job_listing_meta_after' );