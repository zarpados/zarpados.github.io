<?php


if ( !class_exists( 'Shoestrap_Advanced' ) ) {

	/**
	* The "Advanced" module
	*/
	class Shoestrap_Advanced {

		function __construct() {
			global $ss_settings;

			add_action( 'wp_enqueue_scripts', array( $this, 'user_css'           ), 101 );
			add_action( 'wp_footer',          array( $this, 'user_js'            ), 200 );
			add_action( 'wp_footer',          array( $this, 'google_analytics'   ), 20  );
			add_action( 'wp_enqueue_scripts', array( $this, 'scripts'            ), 100 );

			if ( isset( $ss_settings['nice_search'] ) && $ss_settings['nice_search'] == 1 ) {
				add_action( 'template_redirect', array( $this, 'nice_search_redirect' ) );
			}

			/**
			 * Post Excerpt Length
			 * Length in words for excerpt_length filter (http://codex.wordpress.org/Plugin_API/Filter_Reference/excerpt_length)
			 */
			if ( isset( $ss_settings['post_excerpt_length'] ) ) {
				define( 'POST_EXCERPT_LENGTH', $ss_settings['post_excerpt_length'] );
			}

		}

		/**
		* Utility function
		*/
		public static function add_filters( $tags, $function ) {
			foreach( $tags as $tag ) {
				add_filter( $tag, $function );
			}
		}

		/*
		 * echo any custom CSS the user has written to the <head> of the page
		 */
		function user_css() {
			$settings = get_option( SHOESTRAP_OPT_NAME );
			$header_scripts = $settings['user_css'];

			if ( trim( $header_scripts ) != '' ) {
				wp_add_inline_style( 'knowledgepress_css', $header_scripts );
			}
		}

		/*
		 * echo any custom JS the user has written to the footer of the page
		 */
		function user_js() {
			$settings = get_option( SHOESTRAP_OPT_NAME );
			$footer_scripts = $settings['user_js'];

			if ( trim( $footer_scripts ) != '' ) {
				echo '<script id="core.advanced-user-js">' . $footer_scripts . '</script>';
			}
		}

		/**
		 * The Google Analytics code
		 */
		function google_analytics() {
			$settings = get_option( SHOESTRAP_OPT_NAME );
			$analytics_id = $settings['analytics_id'];

			if ( !is_null( $analytics_id ) && !empty( $analytics_id ) ) {
				echo "<script>(function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;e=o.createElement(i);r=o.getElementsByTagName(i)[0];e.src='//www.google-analytics.com/analytics.js';r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));ga('create','" . $analytics_id . "');ga('send','pageview');</script>";
			}
		}

		/**
		 * Redirects search results from /?s=query to /search/query/, converts %20 to +
		 *
		 * @link http://txfx.net/wordpress-plugins/nice-search/
		 */
		function nice_search_redirect() {
			global $wp_rewrite;

			if ( !isset( $wp_rewrite ) || !is_object( $wp_rewrite ) || !$wp_rewrite->using_permalinks() ) {
				return;
			}

			$search_base = $wp_rewrite->search_base;
			if ( is_search() && !is_admin() && strpos( $_SERVER['REQUEST_URI'], "/{$search_base}/" ) === false ) {
				wp_redirect( home_url( "/{$search_base}/" . urlencode( get_query_var( 's' ) ) ) );
				exit();
			}
		}

		/**
		 * Enqueue some extra scripts
		 */
		function scripts() {
			$settings = get_option( SHOESTRAP_OPT_NAME );

			if ( $settings['retina_toggle'] == 1 ) {
				wp_register_script( 'retinajs', SHOESTRAP_ASSETS_URL . '/js/vendor/retina.js', false, null, true );
				wp_enqueue_script( 'retinajs' );
			}
		}

	}
}
