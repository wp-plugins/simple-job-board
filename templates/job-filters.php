<?php do_action( 'simple_job_board_job_filters_before', $atts );

if( 'yes' === get_option ( 'job_board_search_bar' )  || 'yes' === get_option ( 'job_board_category_filter' ) || 'yes' === get_option ( 'job_board_jobtype_filter' ) || 'yes' === get_option ( 'job_board_location_filter' )  ) {
?>
<form class="job-filters">
    <?php do_action( 'simple_job_board_job_filters_start', $atts ); ?>
    <div class="search-jobs">
        <?php if( 'yes' === get_option ( 'job_board_search_bar' ) )
            {
            ?>
        <div class="search-keywords">
            <?php $search_keyword = isset($_GET['search_keywords']) ? $_GET['search_keywords']: '' ; 
            
            // Append Query string With Page ID When Permalinks are not Set
            if ( ! get_option('permalink_structure') ) { 
                ?>
            <input type="hidden" value="<?php echo get_the_ID(); ?>" name="page_id" >
            <?php }?>
            <input type="text" value="<?php echo $search_keyword; ?>" placeholder="<?php _e('Keywords' , 'simple-job-board' );?>" id="search_keywords" name="search_keywords">
            
        </div>
        <?php }
        ?>
        <div class="search-categories">
            <?php            
            
            // check for setting page option and the term existance
            if ( (NULL != get_terms ( 'jobpost_category' )) && ('yes' === get_option ( 'job_board_category_filter' )) ) {
                 
                $selected_category = isset($_GET['selected_category']) ? $_GET['selected_category']: FALSE ;
                
                // Creating List on Non-empty Job Categories
                $category_select = wp_dropdown_categories ( array (
                    'show_option_none' => __( 'Select Category' , 'simple-job-board' ),
                    'hide_empty'       => 0,
                    'echo'             => FALSE,
                    'hierarchical'     => TRUE,
                    'name'             => 'selected_category',
                    'selected'         => $selected_category,
                    'taxonomy'         => 'jobpost_category',
                    'value_field'      => 'slug',
                ) );
                
                if ( isset ($category_select) && (NULL != $category_select ) ) {
                    echo $category_select;
                }
            }
            ?>
        </div>
        <div class="search-job-type">
            <?php
            
            // Check For Settings Option and the Term Existance
            if ( NULL != get_terms ( 'jobpost_job_type' ) && 'yes' === get_option ( 'job_board_jobtype_filter' ) ) {
                
                $selected_jobtype = isset($_GET['selected_jobtype']) ? $_GET['selected_jobtype']: FALSE ;
                
                // Creating list on non-empty job types       
                $jobtype_select = wp_dropdown_categories ( array (
                    'show_option_none' => __( 'Select Job Type' , 'simple-job-board' ),
                    'hide_empty'       => 0,
                    'echo'             => FALSE,
                    'hierarchical'     => true,
                    'name'             => 'selected_jobtype',
                    'taxonomy'         => 'jobpost_job_type',
                    'selected'         => $selected_jobtype,
                    'value_field'      => 'slug',
                ) );
                if ( NULL != $jobtype_select )
                    echo $jobtype_select;
            }
            ?>
        </div>
        <div class="search-location">
            <?php
            
            // Check For Settings Option and the Term Existance
            if ( NULL != get_terms ( 'jobpost_location' ) && 'yes' === get_option ( 'job_board_location_filter' ) ) {
                $selected_location = isset($_GET['selected_location']) ? $_GET['selected_location']: FALSE ;
                
                // Creating list on non-empty job location
                $jobloc_select = wp_dropdown_categories ( array (
                    'hide_empty'       => 0,
                    'name'             => 'selected_location',
                    'hierarchical'     => true,
                    'value_field'      => 'slug',
                    'taxonomy'         => 'jobpost_location',
                    'show_option_none' => __( 'Select Location' , 'simple-job-board' ),
                    'selected'         => $selected_location,
                    'echo'             => FALSE,
                ) );
                if ( NULL != $jobloc_select )
                    echo $jobloc_select;
            }
            ?>
        </div>
        <div class="search-button">
            <?php
            if ( (isset($category_select) && NULL != $category_select) || ( isset($jobtype_select) && NULL != $jobtype_select) || (  isset($jobloc_select) && NULL != $jobloc_select) || 'yes' === get_option ( 'job_board_search_bar' ) )
                echo '<input type="submit" value="'.__( 'Search' , 'simple-job-board' ).'" class="button cs-btn cs-btn-flat cs-btn-rounded cs-btn-flat-accent cs-btn-xxs"><br><br>';
            ?>
        </div>
    </div>
    <?php do_action( 'simple_job_board_job_filters_end', $atts ); ?>
</form>
<?php 
 } 

do_action( 'simple_job_board_job_filters_after', $atts );