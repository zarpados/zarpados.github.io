<?php
global $ss_settings;

define( 'themeURI', get_template_directory_uri() );
define( 'themeFOLDER', get_template() );
define( 'themePATH', get_theme_root() );
define( 'themeNAME', wp_get_theme() );

if ( ! function_exists( 'shoestrap_getVariable' ) ) :
/*
 * Gets the current values from REDUX, and if not there, grabs the defaults
 */
function shoestrap_getVariable( $name, $key = false ) {
	global $redux;
	$options = $redux;

	// Set this to your preferred default value
	$var = '';

	if ( empty( $name ) && ! empty( $options ) ) {
		$var = $options;
	} else {
		if ( ! empty( $options[$name] ) ) {
			$var = ( ! empty( $key ) && ! empty( $options[$name][$key] ) && $key !== true ) ? $options[$name][$key] : $var = $options[$name];;
		}
	}
	return $var;
}
endif;


if ( ! function_exists( 'shoestrap_replace_reply_link_class' ) ) :
/*
 * Apply the proper classes to comment reply links
 */
function shoestrap_replace_reply_link_class( $class ) {
	global $ss_framework;
	$class = str_replace( "class='comment-reply-link", "class='comment-reply-link " . $ss_framework->button_classes( 'primary', 'small' ), $class );
	return $class;
}
endif;
add_filter('comment_reply_link', 'shoestrap_replace_reply_link_class');


if ( ! function_exists( 'shoestrap_init_filesystem' ) ) :
/*
 * Initialize the Wordpress filesystem, no more using file_put_contents function
 */
function shoestrap_init_filesystem() {
	if ( empty( $wp_filesystem ) ) {
		require_once(ABSPATH .'/wp-admin/includes/file.php');
		WP_Filesystem();
	}
}
endif;
add_filter('init', 'shoestrap_init_filesystem');


if ( ! function_exists( 'shoestrap_array_delete' ) ) :
/*
 * Unset a row from an array.
 */
function shoestrap_array_delete( $idx, $array ) {  
	unset( $array[$idx] );
	return ( is_array( $array ) ) ? array_values( $array ) : null;
}
endif;

/*
 * Fonts
 */

function shoestrap_process_font( $font ) {

	if ( empty( $font['font-weight'] ) ) {
		$font['font-weight'] = "inherit";
	}

	if ( empty( $font['font-style'] ) ) {
		$font['font-style'] = "inherit";
	}

	if ( isset( $font['font-size'] ) ) {
		$font['font-size'] = filter_var( $font['font-size'], FILTER_SANITIZE_NUMBER_INT );
	}

	return $font;
}


/**
 * Post format icons
 */
function post_icon($post_id = '') {
    if (get_post_format($post_id) == 'video') {
        return '<i class="kp-w kp-film3"></i> ';
    } elseif (get_post_format($post_id) == 'image') {
        return '<i class="kp-w kp-image4"></i> ';
    } elseif (get_post_format($post_id) == 'audio') {
        return '<i class="kp-w kp-volume-medium3"></i> ';
    } elseif (get_post_format($post_id) == 'link') {
        return '<i class="kp-w kp-link2"></i> ';
    } else {
        return '<i class="kp-w kp-file-text2"></i> ';
    }
}

/**
 * Category icons
 */
function category_icon($cat_id = '') {
	return '<i class="kp-w kp-folder4"></i> ';
}

/**
 * Category open icons
 */
function category_open_icon($cat_id = '') {
	return '<i class="kp-w kp-folder-open3"></i> ';
}
/*
 * Live Search
 */

add_action('wp_ajax_search_title', 'pa_live_search');  // hook for login users
add_action('wp_ajax_nopriv_search_title', 'pa_live_search'); // hook for not login users

function pa_live_search() {
    global $wpdb, $ss_settings;
    
    $post_status	=	'publish';
    $search_term	=	"%".$_REQUEST['query']."%";

    if ($ss_settings['search_post_types']) {
    	$post_type = "'" . implode("','", $ss_settings['search_post_types']) . "'";
    } else {
    	$post_type = "'post'";
    }

	if ($ss_settings['live_search_in'] == '2') {
		$sql_query = $wpdb->prepare( "SELECT ID, post_title, post_type, post_content as post_content, post_name from $wpdb->posts where post_status = %s and post_type in ( $post_type )and (post_title like %s or post_content like %s)", $post_status, $search_term, $search_term );
    } else {
		$sql_query = $wpdb->prepare( "SELECT ID, post_title, post_type, post_content as post_content, post_name from $wpdb->posts where post_status = %s and post_type in ( $post_type )and post_title like %s", $post_status, $search_term );
    }
	
	$results = $wpdb->get_results($sql_query);
	
	$search_json = array( "query" => "Unit", "suggestions" => array() );   // create a json array
	
	foreach ( $results as $result ) {

		$link	=	get_permalink( $result->ID ); // get post url
		$icon   =   post_icon($result->ID);

		$search_icon =	$icon;

    	$search_css = '';

		$search_json["suggestions"][] = array(
											"value" => $result->post_title,
											"data"  => array( "content" => $result->post_content, "url" => $link ),
											"icon" => $search_icon,
										//	"type_label" => $search_post_type,
											"type_color" => $result->post_type,
											"css" => $search_css,
										);
	}
	echo json_encode($search_json); // convert array to joson string
	die();
}

/**
 * Force the selected layout
 */
function pa_force_layout() {
	global $post, $ss_layout, $knowledgepress, $meta;
	$meta = redux_post_meta( 'knowledgepress', get_the_ID() );

	if ( isset( $meta['page_custom_layout'] ) && $meta['page_custom_layout'] != '' ) {
		$layout = $meta['page_custom_layout'];
	} else {
		$layout = 'd';
	}

	// No need to continue if we've selected the default option.
	if ( 'd' == $layout ) {
		return;
	}

	if ( 'f' == $layout ) { // Full-width

		$ss_layout->set_layout( 0 );
		add_filter( 'shoestrap_display_primary_sidebar', '__return_false', 999 );
		add_filter( 'shoestrap_display_secondary_sidebar', '__return_false', 999 );

	} elseif ( 'r'  == $layout ) { // Right Sidebar

		$ss_layout->set_layout( 1 );
		add_filter( 'shoestrap_display_primary_sidebar', '__return_true', 999 );
		add_filter( 'shoestrap_display_secondary_sidebar', '__return_false', 999 );

	} elseif ( 'l'  == $layout ) { // Left Sidebar

		$ss_layout->set_layout( 2 );
		add_filter( 'shoestrap_display_primary_sidebar', '__return_true', 999 );
		add_filter( 'shoestrap_display_secondary_sidebar', '__return_false', 999 );

	} elseif ( 'll'  == $layout ) { // 2 Left Sidebars

		$ss_layout->set_layout( 3 );
		add_filter( 'shoestrap_display_primary_sidebar', '__return_true', 999 );
		add_filter( 'shoestrap_display_secondary_sidebar', '__return_true', 999 );

	} elseif ( 'rr'  == $layout ) { // 2 Right Sidebars

		$ss_layout->set_layout( 4 );
		add_filter( 'shoestrap_display_primary_sidebar', '__return_true', 999 );
		add_filter( 'shoestrap_display_secondary_sidebar', '__return_true', 999 );

	} elseif ( 'lr'  == $layout ) { // 1 Left & 1 Right Sidebars

		$ss_layout->set_layout( 5 );
		add_filter( 'shoestrap_display_primary_sidebar', '__return_true', 999 );
		add_filter( 'shoestrap_display_secondary_sidebar', '__return_true', 999 );

	}
}
add_action( 'wp', 'pa_force_layout' );

/*

function ssep_return_f() { return 'f'; }

function ssep_return_l() { return 'l'; }

function ssep_return_r() { return 'r'; }

function ssep_return_ll() { return 'll'; }

function ssep_return_rr() { return 'rr'; }

function ssep_return_lr() { return 'lr'; }
*/

/*-----------------------------------------------------------------------------------*/
/* Add facebook, twitter, & google+ links to the user profile */
/*-----------------------------------------------------------------------------------*/

function pressapps_add_user_fields( $contactmethods ) {
	// Add Facebook
	$contactmethods['user_fb'] = 'Facebook';
	// Add Twitter
	$contactmethods['user_tw'] = 'Twitter';
	// Add Google+
	$contactmethods['google_profile'] = 'Google Profile URL';
	// Save 'Em
	return $contactmethods;
}
add_filter('user_contactmethods','pressapps_add_user_fields',10,1);


/*-----------------------------------------------------------------------------------*/
/* Post Reorder */
/*-----------------------------------------------------------------------------------*/

/* back end */
global $pagenow, $ss_settings;

if($ss_settings['reorder']) {

	if( $pagenow == 'edit.php') {
	    if ( !isset($_GET['post_type'])  || 'post' == $_GET['post_type'] ) {
	        add_filter( 'pre_get_posts', 'pa_order_reorder_list' );
	        
	    }
	} elseif( $pagenow == 'edit-tags.php' ) {
	    if ( isset($_GET['taxonomy']) && 'category' == $_GET['taxonomy'] ) {
	        add_filter( 'get_terms_orderby', 'pa_order_reorder_taxonomies_list', 10, 2 );
	    }
	} 

	add_action('wp_ajax_pa_order_update_posts', 'pa_order_save_order');
	add_action('wp_ajax_pa_order_update_taxonomies', 'pa_order_save_taxonomies_order');

}

function pa_order_reorder_taxonomies_list($orderby, $args) {
    $orderby = "t.term_group";
    return $orderby;
}

function pa_order_reorder_list($query) {
    $query->set('orderby', 'menu_order');
    $query->set('order', 'ASC');
    return $query;
}

function pa_order_save_order() {
    
    global $wpdb;
    
    $action             = $_POST['action']; 
    $posts_array        = $_POST['post'];
    $listing_counter    = 1;
    
    foreach ($posts_array as $post_id) {
        
        $wpdb->update( 
                    $wpdb->posts, 
                        array('menu_order'  => $listing_counter), 
                        array('ID'          => $post_id) 
                    );

        $listing_counter++;
    }
    
    die();
}

function pa_order_save_taxonomies_order() {
    global $wpdb;
    
    $action             = $_POST['action']; 
    $tags_array         = $_POST['tag'];
    $listing_counter    = 1;
    
    foreach ($tags_array as $tag_id) {
        
        $wpdb->update( 
                    $wpdb->terms, 
                        array('term_group'          => $listing_counter), 
                        array('term_id'     => $tag_id) 
                    );

        $listing_counter++;
    }
    
    die();
}

/* front end */
function pa_reorder_front_end_posts( $query ) {
    if ( is_admin() || ( !$query->is_main_query() && !is_page_template( 'template-knowledgebase.php' ) ) )
        return;

    if ( !isset($query->query_vars['post_type']) || ( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'post' ) ) {
        $query->set( 'orderby', 'menu_order' );
        $query->set( 'order', 'ASC' );       
        return;
    }
}

function pa_reorder_front_end_tax($orderby) {

	$orderby = "t.term_group";
	
	return $orderby;

}

if ( !is_admin() && $ss_settings['reorder'] ) {
	add_filter( 'get_terms_orderby', 'pa_reorder_front_end_tax' );
	add_action( 'pre_get_posts', 'pa_reorder_front_end_posts', 1 );
}


/*-----------------------------------------------------------------------------------*/
/* Video Embed Code Metabox */
/*-----------------------------------------------------------------------------------*/

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function pa_add_meta_box() {

	$screens = array( 'post' );

	foreach ( $screens as $screen ) {

		add_meta_box(
			'video_metabox',
			__( 'Video Embed Code', 'knowledgepress' ),
			'pa_meta_box_callback',
			$screen
		);
	}
}
add_action( 'add_meta_boxes', 'pa_add_meta_box' );

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function pa_meta_box_callback( $post ) {

	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'pa_meta_box', 'pa_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$value = get_post_meta( $post->ID, '_video', true );

	echo '<textarea id="video_embed" name="video_embed" rows="4" cols="40" style="width:98%">' . esc_attr( $value ) . '</textarea>';
	echo '<p>';
	_e( 'Enter video embed code', 'knowledgepress' );
	echo '</p> ';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function pa_save_meta_box_data( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['pa_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['pa_meta_box_nonce'], 'pa_meta_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, its safe for us to save the data now. */
	
	// Make sure that it is set.
	if ( ! isset( $_POST['video_embed'] ) ) {
		return;
	}

	// Sanitize user input.
	//$my_data = sanitize_text_field( $_POST['video_embed'] );
	$my_data = $_POST['video_embed'];

	// Update the meta field in the database.
	update_post_meta( $post_id, '_video', $my_data );
}
add_action( 'save_post', 'pa_save_meta_box_data' );

/**
 * Disable Visual composer update notice
 */
function pa_vcSetAsTheme() {
    vc_set_as_theme();
}
add_action( 'vc_before_init', 'pa_vcSetAsTheme' );


