<?php


if ( ! class_exists( 'Shoestrap_Global' ) ) {

	/**
	* The Header module
	*/
	class Shoestrap_Global {

		function __construct() {

			add_action( 'wp_enqueue_scripts', array( $this, 'css' ), 101 );

		}

		/*
		 * Global CSS is generated here
		 */
		function css() {
			global $ss_settings, $knowledgepress, $meta;
			$meta = redux_post_meta( 'knowledgepress', get_the_ID() );


			$style  = '';

			// Primary color
			$color_brand_primary 		= $ss_settings['color_brand_primary'];
			if ( !empty( $color_brand_primary ) ) {
				$style .= 'a { color: ' . $color_brand_primary . '; }';
				$style .= '.btn-primary { background-color: ' . $color_brand_primary . '; border-color: ' . $color_brand_primary . '; }';
				$style .= '.pagination ul li a:hover, .pagination ul li a:focus, .pagination > .active > a, .pagination > .active > span, .pagination > .active > a:hover, .pagination > .active > span:hover, .pagination > .active > a:focus, .pagination > .active > span:focus { background-color: ' . $color_brand_primary . '; border-color: ' . $color_brand_primary . '; }';
				$style .= '.pagination > li > a, .pagination > li > span { color: ' . $color_brand_primary . '; }';
				$style .= '.hentry .entry-title i { color: ' . $color_brand_primary . '; }';
				$style .= 'input[type="text"]:focus, input[type="email"]:focus, input[type="url"]:focus, input[type="tel"]:focus, input[type="number"]:focus, textarea:focus, select:focus, input[type="date"]:focus, input[type="email"]:focus, .form-control:focus { border-color: ' . $color_brand_primary . '; }';
				$style .= '.autocomplete-suggestion h4 strong { color: ' . $color_brand_primary . '; }';
				// Navlist styling for custom menu widget
				$style .= '.nav-list-primary, .nav-list-primary > li, .nav-list-primary .nav-sublist > li { border-color: ' . $color_brand_primary . '; }';
				$style .= '.nav-list-primary > li > a:hover, .nav-list-primary .nav-sublist > li > a:hover { background-color: ' . $color_brand_primary . '; }';
			


			}

			// Primary hover color
			$color_brand_primary_hover 	= $ss_settings['color_brand_primary_hover'];
			if ( !empty( $color_brand_primary_hover ) ) {
				$style .= 'a:hover { color: ' . $color_brand_primary_hover . '; }';
				$style .= '.btn-primary:hover, .btn-primary:focus { background-color: ' . $color_brand_primary_hover . '; border-color: ' . $color_brand_primary_hover . '; }';
			}

			// Border radius
			if ( !empty( $ss_settings['general_border_radius'] ) ) {
				$style .= '.btn { border-radius: ' . $ss_settings['general_border_radius'] . 'px; }';
				$style .= '.nav-list-primary { border-radius: ' . $ss_settings['general_border_radius'] . 'px; }';
				$style .= '.navbar-default .navbar-toggle { border-radius: ' . $ss_settings['general_border_radius'] . 'px; }';
				$style .= '.form-control { border-radius: ' . $ss_settings['general_border_radius'] . 'px; }';
				$style .= '.btn-group > .btn, .btn-group.social-share > .btn:first-child:not(:last-child):not(.dropdown-toggle){ border-radius: ' . $ss_settings['general_border_radius'] . 'px; }';
				$style .= '.toc { border-radius: ' . $ss_settings['general_border_radius'] . 'px; }';

				
			}


/*
			if ( isset( $meta['page_header_top_padding'] ) && $meta['page_header_top_padding'] > 0 ) {
				$top_padding = $meta['page_header_top_padding'];
			} else {
				$top_padding = $ss_settings['header_top_padding'];
			}
*/

			wp_add_inline_style( 'knowledgepress_css', $style );
		}
	}
}





