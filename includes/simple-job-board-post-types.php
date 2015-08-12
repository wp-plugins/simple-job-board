<?php

/**
 * Custom Post Jobs.
 */
function job_board_register ()
{
    $labels = array (
        'add_new' => 'Add New Job',
        'add_new_item' => 'New Job',
        'edit_item' => 'Edit Job',
        'all_items' => 'All Jobs',
    );
    $args = array (
        'label' => __ ( 'Job Board', 'presstigers' ),
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => false,
        'menu_icon' => 'dashicons-clipboard',
        'description' => __ ( 'Job Posting' ),
        'supports' => array ( 'title', 'editor' ),
        'rewrite' => array ( 'slug' => 'jobs' ),
    );
    register_post_type ( 'jobpost', $args );

    // Add Taxonomy Category, make it hierarchical (like categories)
    $labels = array (
        'name' => _x ( 'Categories', 'taxonomy singular name', 'presstigers' ),
        'singular_name' => _x ( 'Category', 'taxonomy singular name', 'presstigers' ),
        'search_items' => __ ( 'Search Categories' ),
        'all_items' => __ ( 'All Categories' ),
        'parent_item' => __ ( 'Parent Category' ),
        'parent_item_colon' => __ ( 'Parent Category:' ),
        'edit_item' => __ ( 'Edit Category' ),
        'update_item' => __ ( 'Update Category' ),
        'add_new_item' => __ ( 'Add New Category' ),
        'new_item_name' => __ ( 'New Category Name' ),
        'menu_name' => __ ( 'Job Categories' ),
    );

    $args = array (
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array ( 'slug' => 'jobcat' ),
    );

    register_taxonomy ( 'jobpost_category', array ( 'jobpost', ), $args );

    // Add Taxonomy Job Type, make it hierarchical (like categories)
    $labels = array (
        'name' => _x ( 'Job Type', 'taxonomy singular name', 'presstigers' ),
        'singular_name' => _x ( 'Job Type', 'taxonomy singular name', 'presstigers' ),
        'search_items' => __ ( 'Search Job Types' ),
        'all_items' => __ ( 'All Job Types' ),
        'parent_item' => __ ( 'Parent Job Type' ),
        'parent_item_colon' => __ ( 'Parent Job Type:' ),
        'edit_item' => __ ( 'Edit Job Type' ),
        'update_item' => __ ( 'Update Job Type' ),
        'add_new_item' => __ ( 'Add New Job Type' ),
        'new_item_name' => __ ( 'New Job Type Name' ),
        'menu_name' => __ ( 'Job Types' ),
    );

    $args = array (
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array ( 'slug' => 'jobtype' ),
    );

    register_taxonomy ( 'jobpost_job_type', array ( 'jobpost', ), $args );

    // Add Taxonomy, make it hierarchical (like categories)
    $labels = array (
        'name' => _x ( 'Job Location', 'taxonomy singular name', 'presstigers' ),
        'singular_name' => _x ( 'Job Location', 'taxonomy singular name', 'presstigers' ),
        'search_items' => __ ( 'Search Job Locations' ),
        'all_items' => __ ( 'All Job Locations' ),
        'parent_item' => __ ( 'Parent Job Location' ),
        'parent_item_colon' => __ ( 'Parent Job Location:' ),
        'edit_item' => __ ( 'Edit Job Location' ),
        'update_item' => __ ( 'Update Job Location' ),
        'add_new_item' => __ ( 'Add New Job Location' ),
        'new_item_name' => __ ( 'New Job Location Name' ),
        'menu_name' => __ ( 'Job Locations' ),
    );

    $args = array (
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array ( 'slug' => 'job_location' ),
    );

    register_taxonomy ( 'jobpost_location', array ( 'jobpost', ), $args );
}

//Hook - Custom Post i.e. Jobs.
add_action ( 'init', 'job_board_register' );

// Taxonomy -> Job Category ->  Add New Column
function job_board_category_column ( $columns )
{
    $columns[ 'category_column' ] = 'Shortcode';
    return $columns;
}

// Hook - Taxonomy -> Job Category ->  Add New Column
add_filter ( 'manage_edit-jobpost_category_columns', 'job_board_category_column' );

// Taxonomy -> Job Category ->  Add Value to New Column
function job_board_category_column_value ( $deprecated, $column_name, $term_id )
{
    $term = get_term_by ( 'id', $term_id, 'jobpost_category' );

    if ( $column_name == 'category_column' ) {
        echo '[jobpost category="' . $term->slug . '"]';
    }
}

// Hook - Taxonomy -> Job Category ->  Add Value to New Column
add_filter ( 'manage_jobpost_category_custom_column', 'job_board_category_column_value', 10, 3 );

// Taxonomy -> Job Type ->  Add New Column
function job_board_job_type_column ( $columns )
{
    $columns[ 'job_type_column' ] = 'Shortcode';
    return $columns;
}

// Hook - Taxonomy -> Job Type ->  Add New Column
add_filter ( 'manage_edit-jobpost_job_type_columns', 'job_board_job_type_column' );

// Taxonomy -> Job Type ->  Add Value to New Column
function job_board_job_type_column_value ( $deprecated, $column_name, $term_id )
{
    $term = get_term_by ( 'id', $term_id, 'jobpost_job_type' );

    if ( $column_name == 'job_type_column' ) {
        echo '[jobpost type="' . $term->slug . '"]';
    }
}

// Hook - Taxonomy -> Job Type ->  Add Value to New Column
add_filter ( 'manage_jobpost_job_type_custom_column', 'job_board_job_type_column_value', 10, 3 );

// Taxonomy -> Job Location ->  Add New Column
function job_board_job_location_column ( $columns )
{
    $columns[ 'job_location_column' ] = 'Shortcode';
    return $columns;
}

// Hook - Taxonomy -> Job Job Type ->  Add New Column
add_filter ( 'manage_edit-jobpost_location_columns', 'job_board_job_location_column' );

// Taxonomy -> Job Location ->  Add Value to New Column
function job_board_job_location_column_value ( $deprecated, $column_name, $term_id )
{
    $term = get_term_by ( 'id', $term_id, 'jobpost_location' );

    if ( $column_name == 'job_location_column' ) {
        echo '[jobpost location="' . $term->slug . '"]';
    }
}

// Hook - Taxonomy -> Job Location ->  Add Value to New Column
add_filter ( 'manage_jobpost_location_custom_column', 'job_board_job_location_column_value', 10, 3 );

/**
 * Custom Post Applicants.
 */
function jobpost_applicants ()
{
    $lables = array ( 'edit_item' => 'Update Application' );
    $args = array (
        'label' => __ ( 'Applicants', 'presstigers' ),
        'labels' => $lables,
        'public' => true,
        'show_in_nav_menus' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'map_meta_cap' => true,
        'show_in_menu' => 'edit.php?post_type=jobpost',
        'description' => __ ( 'List of Applicants with their resume', 'presstigers' ),
        'supports' => array ( 'editor' ),
        'capabilities' => array (
            'create_posts' => false,
        )
    );
    register_post_type ( 'jobpost_applicants', $args );
}

// Hook - Custom Post Applicants.
add_action ( 'init', 'jobpost_applicants' );

// Delete Uploads on Applicant Deletion 
function job_board_delete_uploads ( $postId )
{
    global $post_type;
    if ( $post_type == 'jobpost_applicants' && '' != get_post_meta ( $postId, 'resume_path', TRUE ) )
        unlink ( get_post_meta ( $postId, 'resume_path', TRUE ) );
}

// Hook - Delete Uploads on Applicant Deletion
add_action ( 'before_delete_post', 'job_board_delete_uploads' );

// Applicant Listing - Column Name
function job_board_applicant_list_columns ( $columns )
{
    $columns = array (
        'cb' => '<input type="checkbox" />',
        'title' => __ ( 'Job Applied for', 'presstigers' ),
        'applicant' => __ ( 'Applicant', 'presstigers' ),
        'taxonomy' => __ ( 'Categories', 'presstigers' ),
        'date' => __ ( 'Date', 'presstigers' ),
    );
    return $columns;
}

// Hook - Applicant Listing - Column Name
add_filter ( 'manage_edit-jobpost_applicants_columns', 'job_board_applicant_list_columns' );

// Applicant Listing - Column Value
function job_board_applicant_list_columns_value ( $column, $post_id )
{
    $keys = get_post_custom_keys ( $post_id );
    switch ( $column ) {
        case 'applicant' :
            $applicant_name = sprintf ( '<a href="%s">%s</a>', esc_url ( add_query_arg ( array ( 'post' => $post_id, 'action' => 'edit' ), 'post.php' ) ), esc_html ( get_post_meta ( $post_id, $keys[ 0 ], TRUE ) )
            );
            echo $applicant_name;
            break;
        case 'taxonomy' :
            $parent_id = wp_get_post_parent_id ( $post_id ); // get_post_field ( 'post_parent', $post_id );
            $terms = get_the_terms ( $parent_id, 'jobpost_category' );
            if ( !empty ( $terms ) ) {
                $out = array ();
                foreach ( $terms as $term ) {
                    $out[] = sprintf ( '<a href="%s">%s</a>', esc_url ( add_query_arg ( array ( 'post_type' => get_post_type ( $parent_id ), 'jobpost_category' => $term->slug ), 'edit.php' ) ), esc_html ( sanitize_term_field ( 'name', $term->name, $term->term_id, 'jobpost_category', 'display' ) )
                    );
                }
                echo join ( ', ', $out );
            }/* If no terms were found, output a default message. */ else {
                _e ( 'No Categories' );
            }
            break;
    }
}

// Hook - Applicant Listing - Column Value
add_action ( 'manage_jobpost_applicants_posts_custom_column', 'job_board_applicant_list_columns_value', 10, 2 );
