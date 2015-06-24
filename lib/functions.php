<?php
function jobpost_is_checked($i){
    if($i==0) $checked="checked";
    else $checked = NULL;
    
    return $checked;
}

/*Disable Custom Fields from Job Posts*/
function jobpost_remove_custom_fields() {
	remove_meta_box( 'postcustom' , 'jobpost' , 'normal' ); 
}
add_action( 'admin_menu' , 'jobpost_remove_custom_fields' );

/**
 * Shortcode Generator
 * @param type $atts
 * @return type
 */
function jobpost_shortcode_func( $atts ) {
    $a = shortcode_atts( array(
        'posts' => '-1',
        'excerpt' => 'yes',
    ), $atts );
    
    $args=array(
        'posts_per_page'   => $a['posts'],
        'post_type'     =>'jobpost',
        );
    query_posts( $args );
    function custom_excerpt_more( $more ) {
	return '....';
    }
    add_filter( 'excerpt_more', 'custom_excerpt_more' );
    ob_start();
    echo '<ol class="jobpost_list">';
    if(have_posts()): while(have_posts()): the_post();
    ?>
        <li>
            <strong><?php the_title(); ?></strong>
            <?php if($a['excerpt'] == 'yes') the_excerpt(); ?>
            <a href="<?php the_permalink() ?>">Job Detail</a>
        </li>
    <?php
    endwhile; endif;
    echo '</ol>';
    $html=ob_get_clean();
    wp_reset_query();
    return $html;
}
add_shortcode( 'jobpost', 'jobpost_shortcode_func' );

function jobpost_register(){
    $labels=array(
        'add_new'  => 'Add New Job',
        'add_new_item'  => 'New Job',
        'edit_item'  => 'Edit Job',
        'all_items' => 'All Jobs',
    );
    $args=array(
        'label' => __( 'Job Board', 'presstigers' ),
        'labels'=> $labels,
        'public'=>  true,
        'show_in_nav_menus' => false,
        'menu_icon'  => 'dashicons-clipboard',
        'description' => __( 'Job Posting' ),
        'supports' => array('title', 'editor'),
        'rewrite' => array('slug'=>'jobs'),
    );
    register_post_type('jobpost',$args);
    
    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
            'name'              => _x( 'Categories', 'taxonomy singular name',  'presstigers' ),
            'singular_name'     => _x( 'Category','taxonomy singular name', 'presstigers' ),
            'search_items'      => __( 'Search Categories' ),
            'all_items'         => __( 'All Categories' ),
            'parent_item'       => __( 'Parent Category' ),
            'parent_item_colon' => __( 'Parent Category:' ),
            'edit_item'         => __( 'Edit Category' ),
            'update_item'       => __( 'Update Category' ),
            'add_new_item'      => __( 'Add New Category' ),
            'new_item_name'     => __( 'New Category Name' ),
            'menu_name'         => __( 'Job Categories' ),
    );

    $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'jobcat' ),
    );

    register_taxonomy( 'jobpost_category', array( 'jobpost', ), $args );    
}

add_action( 'init', 'jobpost_register' );

/*Fixing rewrite rules on plugin activation*/
function jobpost_myplugin_activate() {
    jobpost_register();
    flush_rewrite_rules();
}
register_activation_hook( $plugin_path.'/simple_job_board.php', 'jobpost_myplugin_activate' );

function jobpost_applicants(){
    $lables= array('edit_item'=>'Update Application');
    $args=array(
        'label' => __( 'Applicants', 'presstigers' ),
        'labels' => $lables,
        'public'            =>  true,
        'show_in_nav_menus' => false,
        'exclude_from_search'=> true,
        'publicly_queryable' => false,
        'map_meta_cap'      => true,
        'show_in_menu' => 'edit.php?post_type=jobpost',
        'description' => __( 'List of Applicants with their resume', 'presstigers' ),
        'supports' => array('editor'),
        'capabilities' => array(
            'create_posts' => false,
        )
);
    register_post_type('jobpost_applicants',$args);
}
add_action( 'init', 'jobpost_applicants' );


/**Display Applicants Custom Columns**/
add_filter( 'manage_edit-jobpost_applicants_columns', 'jobpost_applicants_columns' ) ;
add_action( 'manage_jobpost_applicants_posts_custom_column' , 'custom_jobpost_applicant_column', 10, 2 );

function jobpost_applicants_columns( $columns ) {
	$columns = array(
            'applicant' => __( 'Applicant', 'presstigers' ),
            'job' => __( 'Job Applied For', 'presstigers' ),
	);
	return $columns;
}

function custom_jobpost_applicant_column( $column, $post_id ) {
    $keys=  get_post_custom_keys($post_id);
    switch ( $column ) {
        case 'applicant' :
            echo '<a class="row-title" href="'.admin_url().'post.php?post='.$post_id.'&action=edit">'.get_post_meta( $post_id , $keys[0] , true ).'</a>';
            break;
        case 'job' :
            echo '<a class="row-title" href="'.admin_url().'post.php?post='.  get_post_field('post_parent', $post_id).'&action=edit">'.get_the_title( $post_id ).'</a>'; 
            break;
        
    }
}


?>
