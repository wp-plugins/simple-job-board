<?php

/**
 * Function to generate filters
 * 
 * Category drop down 
 * Job type drop down
 * Job Location drop down
 */
function job_filter_form ()
{
    if ( 'yes' === get_option ( 'job_board_search_bar' ) ):
        ?>

        <!-- Job Search Form-->
        <div class="search-holder">
            <form action="<?php echo get_home_url (); ?>"> 
                <input type="text" id="search-text" name="s" id="s" placeholder="Search a Job" />
                <button id="search-btn"><i class="fa fa-search"></i></button>
            </form>
        </div><br><br>
        <div class="clearfix clear"></div>

        <?php
    endif;
    // Drop Down for Job Search
    echo '<form id="job_filters" method="post">';
    $category_select = '';
    $jobtype_select = '';
    $jobloc_select = '';

    // check for setting page option and the term existance
    if ( (NULL != get_terms ( 'jobpost_category' )) && ('yes' === get_option ( 'job_board_category_filter' )) ) {

        // Creating List on Non-empty Job Categories
        $category_select = wp_dropdown_categories ( array (
            'hide_empty' => 0,
            'name' => 'category_list',
            'hierarchical' => true,
            'value_field' => 'slug',
            'taxonomy' => 'jobpost_category',
            'show_option_none' => 'Select Category',
            'echo' => FALSE,
                ) );
    }

    // Check For Settings Option and the Term Existance
    if ( NULL != get_terms ( 'jobpost_job_type' ) && 'yes' === get_option ( 'job_board_jobtype_filter' ) ) {

        // Creating list on non-empty job types       
        $jobtype_select = wp_dropdown_categories ( array (
            'hide_empty' => 0,
            'name' => 'jobtype_list',
            'hierarchical' => true,
            'value_field' => 'slug',
            'taxonomy' => 'jobpost_job_type',
            'show_option_none' => 'Select Job Type',
            'echo' => FALSE,
                ) );
    }

    // Check For Settings Option and the Term Existance
    if ( NULL != get_terms ( 'jobpost_location' ) && 'yes' === get_option ( 'job_board_location_filter' ) ) {

        // Creating list on non-empty job location

        $jobloc_select = wp_dropdown_categories ( array (
            'hide_empty' => 0,
            'name' => 'job_location_list',
            'hierarchical' => true,
            'value_field' => 'slug',
            'taxonomy' => 'jobpost_location',
            'show_option_none' => 'Select Location',
            'echo' => FALSE,
                ) );
    }
    echo '<div>';
    if ( NULL != $category_select )
        echo $category_select;
    if ( NULL != $jobtype_select )
        echo $jobtype_select;
    if ( NULL != $jobloc_select )
        echo $jobloc_select;
    if ( (NULL != $category_select) || (NULL != $jobtype_select) || (NULL != $jobloc_select) )
        echo '<input type="submit" value="Search" class="button"><br><br>';
    echo '</div>';
    echo '';
    echo'</form>';
}