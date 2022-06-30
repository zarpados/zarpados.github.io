<?php

global $ss_settings;

if ( ! defined( 'SS_FRAMEWORK' ) ) {
	define( 'SS_FRAMEWORK', 'bootstrap' );
}

if ( ! defined( 'SHOESTRAP_ASSETS_URL' ) ) {
	$shoestrap_assets_url = str_replace( 'http:', '', get_template_directory_uri() . '/assets' );
	$shoestrap_assets_url = str_replace( 'https:', '', $shoestrap_assets_url );
	define( 'SHOESTRAP_ASSETS_URL', $shoestrap_assets_url );
}
if ( ! defined( 'SHOESTRAP_OPT_NAME' ) ) {
	define( 'SHOESTRAP_OPT_NAME', 'knowledgepress' );
}

$ss_settings = get_option( SHOESTRAP_OPT_NAME );

if ( class_exists( 'BuddyPress' ) ) {
	require_once locate_template( '/lib/buddypress.php' );
}

if ( ! class_exists( 'ReduxFramework' ) && ! is_admin() ) {
	_e( 'Install and activate Redux Framework under Appearance > Install Plugins!', 'knowledgepress' );
	function redux_post_meta() {}
}

require_once locate_template( '/lib/redux/config.php');         		// Metaboxes
require_once locate_template( '/lib/class-Shoestrap_Image.php' );		// Image manipulation
require_once locate_template( '/lib/functions-core.php' );
require_once locate_template( '/framework/class-SS_Framework.php' );
require_once locate_template( '/lib/template.php' );     				// Custom get_template_part function.
require_once locate_template( '/lib/init.php' );         				// Initial theme setup and constants
require_once locate_template( '/lib/wrapper.php' );      				// Theme wrapper class
require_once locate_template( '/lib/sidebar.php' );      				// Sidebar class
require_once locate_template( '/lib/footer.php' );       				// Footer configuration
require_once locate_template( '/lib/config.php' );       				// Configuration
require_once locate_template( '/lib/popular.php' );       				// Popular posts
require_once locate_template( '/lib/titles.php' );       				// Page titles
require_once locate_template( '/lib/cleanup.php' );      				// Cleanup
require_once locate_template( '/lib/comments.php' );     				// Custom comments modifications
require_once locate_template( '/lib/widgets.php' );      				// Sidebars and widgets
require_once locate_template( '/lib/voting.php' );      				// Voting
require_once locate_template( '/lib/post-formats.php' ); 				// Sidebars and widgets
require_once locate_template( '/lib/scripts.php' );      				// Scripts and stylesheets
require_once locate_template( '/lib/dependencies.php' ); 				// Load our dependencies
if ( class_exists( 'bbPress' ) ) {
	require_once locate_template( '/lib/bbpress.php' );
}
require_once locate_template( '/lib/admin/init.php' );         				
require_once locate_template( '/lib/admin/search-track.php' );         				
