<?php global $post; ?>
<li <?php post_class ( 'row job-listing' ); ?>>
    <a href="<?php the_permalink (); ?>">
        <div class="job-position col-md-6 col-sm-8 col-xs-12">
            <div class="company-logo pull-left"><?php sjb_the_company_logo (); ?></div>
            <?php
            $showCompany = 0;
            $class_company_margin = 'topmargin';
            if ( get_post_meta ( $post->ID, 'simple_job_board_company_name', TRUE ) ) {
                $class_company_margin = '';
                $showCompany = 1;
            }
            ?>

            <div class="job-company <?php echo $class_company_margin; ?> pull-left">
                <h3><?php the_title (); ?></h3>
                <?php if ( "1" == $showCompany ) { ?>
                    <span><?php echo get_post_meta ( $post->ID, 'simple_job_board_company_name', TRUE ); ?></span>
                <?php } ?>
            </div>
        </div>
        <div class="job-location col-md-3 col-sm-12 col-xs-12">
            <?php sjb_the_job_location (); ?>
        </div>
        <div class="job-meta col-md-3 col-sm-12 col-xs-12">
            <?php sjb_the_job_type (); ?>
        </div>
        <div class="description col-md-12 col-sm-12 col-xs-12">
            <?php the_excerpt (); ?>
        </div>
    </a>
</li>