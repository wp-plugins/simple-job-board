<?php
/**
 * Single view Company information box
 *
 * Hooked into single_job_listing_start priority 30
 *
 * @since  2.1.0
 */

if ( ! sjb_get_the_company_name() ) {
    return;
}
?>
<div class="row company">
    <div class="col-md-2"><?php sjb_the_company_logo(); ?></div>
    <div class="col-md-10">
        <p class="name">
            <?php if ( $website = sjb_get_the_company_website() ) : ?>
                <a class="website" href="<?php echo esc_url( $website ); ?>" target="_blank" rel="nofollow"><?php _e( 'URL', 'simple-job-board' ); ?></a>
            <?php endif; ?>
            <?php sjb_the_company_name( '<strong>', '</strong>' ); ?>
        </p>
        <?php sjb_the_company_tagline( '<p class="tagline">', '</p>' ); ?>
    </div>
</div>