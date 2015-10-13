<?php global $post; ?>
<div class="single-job-listing">
    <?php if ( 'expired' === $post->post_status ) : ?>
        <div class="simple-job-board-info"><?php _e( 'This listing has expired.', 'simple-job-board' ); ?></div>
    <?php else : ?>
        <?php
        /**
         * single_job_listing_start hook
         *
         * @hooked job_listing_meta_display - 20
         * @hooked job_listing_company_display - 30
         */
        do_action( 'single_job_listing_start' );
        ?>
        <div class="job-description">
            <?php apply_filters( 'the_job_description', the_content() ); ?>
        </div>
        <?php
        /**
         * single-job-listing-end hook
         */
        do_action( 'single_job_listing_end' );
        ?>
        <?php get_simple_job_board_template( 'job-application.php' ); ?>
    <?php endif; ?>
</div>