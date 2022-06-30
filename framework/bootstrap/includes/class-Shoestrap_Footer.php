<?php


if( ! class_exists( 'Shoestrap_Footer' ) ) {
	/**
	* Build the Shoestrap Footer module class.
	*/
	class Shoestrap_Footer {

		function __construct() {
			add_action( 'wp_enqueue_scripts',    array( $this, 'css' ), 101 );
			add_action( 'shoestrap_footer_html', array( $this, 'html' ) );
			add_action( 'widgets_init',          array( $this, 'widgets_init' ) );
		}

		/**
		 * Register sidebars and widgets
		 */
		function widgets_init() {
			$class        = '';
			$before_title = apply_filters( 'shoestrap_widgets_before_title', '<h3 class="widget-title">' );
			$after_title  = apply_filters( 'shoestrap_widgets_after_title', '</h3>' );

			// Sidebars

			register_sidebar( array(
				'name'          => __( 'Footer 1', 'knowledgepress' ),
				'id'            => 'sidebar-footer-1',
				'before_widget' => '<section id="%1$s" class="' . $class . 'widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => $before_title,
				'after_title'   => $after_title,
			));

			register_sidebar( array(
				'name'          => __( 'Footer 2', 'knowledgepress' ),
				'id'            => 'sidebar-footer-2',
				'before_widget' => '<section id="%1$s" class="' . $class . 'widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => $before_title,
				'after_title'   => $after_title,
			));

			register_sidebar( array(
				'name'          => __( 'Footer 3', 'knowledgepress' ),
				'id'            => 'sidebar-footer-3',
				'before_widget' => '<section id="%1$s" class="' . $class . 'widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => $before_title,
				'after_title'   => $after_title,
			));

			register_sidebar( array(
				'name'          => __( 'Footer 4', 'knowledgepress' ),
				'id'            => 'sidebar-footer-4',
				'before_widget' => '<section id="%1$s" class="' . $class . 'widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => $before_title,
				'after_title'   => $after_title,
			));
		}

		/**
		 * If the options selected require the insertion of some custom CSS to the document head, generate that CSS here
		 */

		function css() {
			global $ss_settings;

			$bg         = $ss_settings['footer_background'];
			$cl_brand   = $ss_settings['color_brand_primary'];
			$cl_brand_hover   = $ss_settings['color_brand_primary_hover'];
			$border     = $ss_settings['footer_border'];
			$top_margin = $ss_settings['footer_top_margin'];
			$cta 		= is_footer_cta_active();

			$style = '.content-info {';
				$style .= 'background:' . $bg . ';';
				$style .= ( ! empty($border) && $border['border-top'] > 0 && ! empty($border['border-color']) ) ? 'border-top:' . $border['border-top'] . ' ' . $border['border-style'] . ' ' . $border['border-color'] . ';' : '';
				$style .= ( ! empty($top_margin) && !$cta ) ? 'margin-top:'. $top_margin .'px;' : '';
			$style .= '}';
			$style .= '.content a:hover { color:' . $cl_brand_hover . ' }';
			$style .= '.btn-primary :hover { background-color:' . $cl_brand_hover . '!important; border-color:' . $cl_brand_hover . '!important }';

			$style .= '.footer-cta {';
				$style .= ( ! empty($top_margin) && $cta ) ? 'margin-top:'. $top_margin .'px;' : '';
			$style .= '}';

			wp_add_inline_style( 'knowledgepress_css', $style );
		}

		function html() {
			global $ss_framework, $ss_social, $ss_settings;

			// The blogname for use in the copyright section
			$blog_name  = get_bloginfo( 'name', 'display' );

			// The copyright section contents
			if ( isset( $ss_settings['footer_text'] ) ) {
				$ftext = $ss_settings['footer_text'];
			} else {
				$ftext = '&copy; [year] [sitename]';
			}

			// Replace [year] and [sitename] with meaninful content
			$ftext = str_replace( '[year]', date( 'Y' ), $ftext );
			$ftext = str_replace( '[sitename]', $blog_name, $ftext );

			// Do we want to display social links?
			if ( isset( $ss_settings['footer_social_toggle'] ) && $ss_settings['footer_social_toggle'] == 1 ) {
				$social = true;
			} else {
				$social = false;
			}

			// How many columns wide should social links be?
			$social_width = '12';


			// Social is enabled, we're modifying the width!
			if ( $social_width && $social && intval( $social_width ) > 0 ) {
				$width = 12 - intval( $social_width );
			} else {
				$width = 12;
			}

			$blank = ' target="_blank"';

			$networks = $ss_social->get_social_links();

			do_action( 'shoestrap_footer_before_copyright' );

			echo '<div id="footer-copyright">';
				echo $ss_framework->open_row( 'div' );

						if ( $social && ! is_null( $networks ) && count( $networks ) > 0 ) {
							echo '<div id="footer_social_bar">';

								foreach ( $networks as $network ) {
									// Check if the social network URL has been defined
									if ( isset( $network['url'] ) && ! empty( $network['url'] ) && strlen( $network['url'] ) > 7 ) {
										echo '<a href="' . $network['url'] . '"' . $blank . ' title="' . $network['icon'] . '"><span class="kp-' . $network['icon'] . '"></span></a>';
									}
								}

							echo '</div>';
						}

					echo '<div id="copyright-bar">' . $ftext . '</div>';
					echo $ss_framework->close_col( 'div' );

					echo $ss_framework->clearfix();
				echo $ss_framework->close_row( 'div' );
			echo '</div>';
		}
	}
}
