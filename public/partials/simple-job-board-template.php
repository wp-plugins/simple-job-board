<?php

/**
 * Template Functions
 *
 * Template functions specifically created for job listings
 *
 * @author 	PressTigers
 * @category 	Core
 * @package 	Simple Job Board/Template
 * @version     1.0.0
 */
/**
 * Get and include template files.
 *
 * @param   mixed   $template_name
 * @param   array   $args (default: array())
 * @param   string  $template_path (default: '')
 * @param   string  $default_path (default: '')
 * @return  void
 */
function get_simple_job_board_template( $template_name, $args = array (), $template_path = 'simple_job_board', $default_path = '' )
{
    if ( $args && is_array( $args ) ) {
        extract( $args );
    }
    include( locate_simple_job_board_template( $template_name, $template_path, $default_path ) );
}
/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 * yourtheme		/	$template_path	/	$template_name
 * yourtheme		/	$template_name
 * $default_path	/	$template_name
 *
 * @param string $template_name
 * @param string $template_path (default: 'simple_job_board')
 * @param string|bool $default_path (default: '') False to not load a default
 * @return string
 */
function locate_simple_job_board_template( $template_name, $template_path = 'simple_job_board', $default_path = '' )
{
    // Look within passed path within the theme - this is priority
    $template = locate_template(
            array (
                trailingslashit( $template_path ) . $template_name,
                $template_name
            )
    );

    // Get default template
    if ( ! $template && $default_path !== false ) {
        $default_path = $default_path ? $default_path : SIMPLE_JOB_BOARD_PLUGIN_DIR . '/templates/';
        if ( file_exists( trailingslashit( $default_path ) . $template_name ) ) {
            $template = trailingslashit( $default_path ) . $template_name;
        }
    }

    // Return what we found
    return apply_filters( 'simple_job_board_locate_template', $template, $template_name, $template_path );
}
/**
 * Get template part (for templates in loops).
 *
 * @param string $slug
 * @param string $name (default: '')
 * @param string $template_path (default: 'simple_job_board')
 * @param string|bool $default_path (default: '') False to not load a default
 */
function get_simple_job_board_template_part( $slug, $name = '', $template_path = 'simple_job_board', $default_path = '' )
{    
    $template = '';
    
    if ( $name ) {
        $template = locate_simple_job_board_template( "{$slug}-{$name}.php", $template_path, $default_path );
    }

    // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/simple_job_board/slug.php
    if ( ! $template ) {
        $template = locate_simple_job_board_template( "{$slug}.php", $template_path, $default_path );
    }

    if ( $template ) {
        load_template( $template, false );
    }
}
/**
 * Add custom body classes
 * @param  array $classes
 * @return array
 */
function simple_job_board_body_class( $classes )
{
    $classes = ( array ) $classes;
    $classes[] = sanitize_title( wp_get_theme() );

    return array_unique( $classes );
}
add_filter( 'body_class', 'simple_job_board_body_class' );
/**
 * sjb_the_job_category function.
 *
 * @access public
 * @return void
 */
function sjb_the_job_category( $post = NULL )
{
    if ( $job_category = sjb_get_the_job_category( $post ) ) {
        foreach ( $job_category as $cat ) {
            echo "<span class=\"job-category\"> " . $cat->name . "</span>";
        }
    }
}
/**
 * sjb_get_the_job_category function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function sjb_get_the_job_category( $post = NULL )
{
    $post = get_post( $post );
    if ( $post->post_type !== 'jobpost' ) {
        return;
    }

    $categories = wp_get_post_terms( $post->ID, 'jobpost_category' );
    return apply_filters( 'sjb_the_job_category', $categories, $post );
}
/**
 * sjb_the_job_type function.
 *
 * @access public
 * @return void
 */
function sjb_the_job_type( $post = NULL )
{
    if ( $job_type = sjb_get_the_job_type( $post ) ) {
        echo "<div class=\"job-type\"><span class=\"glyphicon glyphicon-time full-time\"></span>". $job_type->name ."</div>";
    }
}

/**
 * sjb_get_the_job_type function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function sjb_get_the_job_type( $post = NULL )
{
    $post = get_post( $post );
    if ( $post->post_type !== 'jobpost' ) {
        return;
    }

    $types = wp_get_post_terms( $post->ID, 'jobpost_job_type' );
    $type = ( $types ) ? current( $types ) : FALSE;

    return apply_filters( 'sjb_the_job_type', $type, $post );
}

/**
 * sjb_the_job_location function.
 *
 * @access public
 * @return void
 */
function sjb_the_job_location( $post = NULL )
{
    $post = get_post( $post );
    if ( $job_location = sjb_get_the_job_location( $post ) ) {
        echo "<span class=\"job-location\"><i class=\"glyphicon glyphicon-map-marker\"></i> " . $job_location->name . "</span>";
    }
}
/**
 * sjb_get_the_job_location function.
 *
 * @access public
 * @param mixed $post (default: NULL)
 * @return void
 */
function sjb_get_the_job_location( $post = NULL )
{
    $post = get_post( $post );

    if ( $post->post_type !== 'jobpost' ) {
        return;
    }

    $locations = wp_get_post_terms( $post->ID, 'jobpost_location' );
    $location = ( $locations ) ? current( $locations ) : FALSE;

    return apply_filters( 'sjb_the_job_location', $location, $post );
}

/**
 * Display or retrieve the current company name with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function sjb_the_company_name( $before = '', $after = '', $echo = true, $post = null )
{
    $company_name = sjb_get_the_company_name( $post );

    if ( strlen( $company_name ) == 0 )
        return;

    $company_name = esc_attr( strip_tags( $company_name ) );
    $company_name = $before . $company_name . $after;

    if ( $echo )
        echo $company_name;
    else
        return $company_name;
}

/**
 * sjb_get_the_company_name function.
 *
 * @access public
 * @param int $post (default: null)
 * @return string
 */
function sjb_get_the_company_name( $post = NULL )
{
    $post = get_post( $post );
    if ( $post->post_type !== 'jobpost' ) {
        return '';
    }

    return apply_filters( 'sjb_the_company_name', $post->simple_job_board_company_name, $post );
}

/**
 * sjb_get_the_company_website function.
 *
 * @access public
 * @param int $post (default: null)
 * @return void
 */
function sjb_get_the_company_website( $post = NULL )
{
    $post = get_post( $post );

    if ( $post->post_type !== 'jobpost' )
        return;

    $website = $post->simple_job_board_company_website;

    if ( $website && ! strstr( $website, 'http:' ) && ! strstr( $website, 'https:' ) ) {
        $website = 'http://' . $website;
    }

    return apply_filters( 'sjb_the_company_website', $website, $post );
}

/**
 * Display or retrieve the current company tagline with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function sjb_the_company_tagline( $before = '', $after = '', $echo = TRUE, $post = NULL )
{
    $company_tagline = sjb_get_the_company_tagline( $post );

    if ( strlen( $company_tagline ) == 0 )
        return;

    $company_tagline = esc_attr( strip_tags( $company_tagline ) );
    $company_tagline = $before . $company_tagline . $after;

    if ( $echo )
        echo $company_tagline;
    else
        return $company_tagline;
}

/**
 * sjb_get_the_company_tagline function.
 *
 * @access public
 * @param int $post (default: 0)
 * @return void
 */
function sjb_get_the_company_tagline( $post = NULL )
{
    $post = get_post( $post );

    if ( $post->post_type !== 'jobpost' )
        return;

    return apply_filters( 'sjb_the_company_tagline', $post->simple_job_board_company_tagline, $post );
}


/**
 * sjb_the_company_logo function.
 *
 * @access public
 * @param string $size (default: 'full')
 * @param mixed $default (default: null)
 * @return void
 */
function sjb_the_company_logo( $size = 'full', $default = NULL, $post = NULL )
{
    $logo = sjb_get_the_company_logo( $post );

    if ( ! empty( $logo ) && ( strstr( $logo, 'http' ) || file_exists( $logo ) ) ) {
        if ( $size !== 'full' ) {
            $logo = simple_job_board_get_resized_image( $logo, $size );
        }
        echo '<img src="' . esc_attr( $logo ) . '" alt="' . esc_attr( sjb_get_the_company_name( $post ) ) . '" />';
    } elseif ( $default ) {
        echo '<img src="' . esc_attr( $default ) . '" alt="' . esc_attr( sjb_get_the_company_name( $post ) ) . '" />';
    } else {
        echo '<img src="' . esc_attr( apply_filters( 'simple_job_board_default_company_logo', plugin_dir_url ( dirname ( __FILE__ ) ) . 'images/company.png' ) ) . '" alt="' . esc_attr( sjb_get_the_company_name( $post ) ) . '" />';
    }
}

/**
 * sjb_get_the_company_logo function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return string
 */
function sjb_get_the_company_logo( $post = NULL )
{
    $post = get_post( $post );
    if ( $post->post_type !== 'jobpost' )
        return;

    return apply_filters( 'sjb_the_company_logo', $post->simple_job_board_company_logo, $post );
}

/**
 * Resize and get url of the image
 *
 * @param  string $logo
 * @param  string $size
 * @return string
 */
function simple_job_board_get_resized_image( $logo, $size )
{
    global $_wp_additional_image_sizes;

    if ( $size !== 'full' && strstr( $logo, WP_CONTENT_URL ) && ( isset( $_wp_additional_image_sizes[ $size ] ) || in_array( $size, array ( 'thumbnail', 'medium', 'large' ) ) ) ) {

        if ( in_array( $size, array ( 'thumbnail', 'medium', 'large' ) ) ) {
            $img_width = get_option( $size . '_size_w' );
            $img_height = get_option( $size . '_size_h' );
            $img_crop = get_option( $size . '_size_crop' );
        } else {
            $img_width = $_wp_additional_image_sizes[ $size ][ 'width' ];
            $img_height = $_wp_additional_image_sizes[ $size ][ 'height' ];
            $img_crop = $_wp_additional_image_sizes[ $size ][ 'crop' ];
        }

        $upload_dir = wp_upload_dir();
        $logo_path = str_replace( array ( $upload_dir[ 'baseurl' ], $upload_dir[ 'url' ], WP_CONTENT_URL ), array ( $upload_dir[ 'basedir' ], $upload_dir[ 'path' ], WP_CONTENT_DIR ), $logo );
        $path_parts = pathinfo( $logo_path );
        $resized_logo_path = str_replace( '.' . $path_parts[ 'extension' ], '-' . $size . '.' . $path_parts[ 'extension' ], $logo_path );

        if ( strstr( $resized_logo_path, 'http:' ) || strstr( $resized_logo_path, 'https:' ) ) {
            return $logo;
        }

        if ( ! file_exists( $resized_logo_path ) ) {
            ob_start();
            $image = wp_get_image_editor( $logo_path );
            if ( ! is_wp_error( $image ) ) {
                $resize = $image->resize( $img_width, $img_height, $img_crop );
                if ( ! is_wp_error( $resize ) ) {
                    $save = $image->save( $resized_logo_path );
                    if ( ! is_wp_error( $save ) ) {
                        $logo = dirname( $logo ) . '/' . basename( $resized_logo_path );
                    }
                }
            }
            ob_get_clean();
        } else {
            $logo = dirname( $logo ) . '/' . basename( $resized_logo_path );
        }
    }

    return $logo;
}

/** 
 * Assign Default Radio button Check
 */
function job_board_is_checked ( $i )
{
    $checked = ( $i == 0 ) ? "checked" : NULL;
    return $checked;
}

/**
 * Displays job meta data on the single job page
 */
function job_listing_meta_display()
{
    get_simple_job_board_template( 'content-single-job-listing-meta.php', array () );
}
add_action( 'single_job_listing_start', 'job_listing_meta_display', 20 );

/**
 * Displays job company data on the single job page
 */
function job_listing_company_display()
{
    get_simple_job_board_template( 'content-single-job-listing-company.php', array () );
}
add_action( 'single_job_listing_start', 'job_listing_company_display', 30 );

/**
 * Displays job features data on the single job page
 */
function job_listing_features()
{
    get_simple_job_board_template( 'job-features.php', array () );
}
add_action( 'single_job_listing_end', 'job_listing_features', 20 );