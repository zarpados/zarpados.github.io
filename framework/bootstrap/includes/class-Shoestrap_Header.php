<?php


if ( ! class_exists( 'Shoestrap_Header' ) ) {

	/**
	* The Header module
	*/
	class Shoestrap_Header {

		function __construct() {
			add_action( 'widgets_init',       array( $this, 'header_widgets_init' ), 30 );
			add_action( 'shoestrap_pre_wrap', array( $this, 'branding' ), 3 );
			add_action( 'wp_enqueue_scripts', array( $this, 'css' ), 101 );

		}

		/**
		 * Register sidebars and widgets
		 */
		function header_widgets_init() {
			register_sidebar( array(
				'name'          => __( 'Header', 'knowledgepress' ),
				'id'            => 'header-area',
				'before_widget' => '<div>',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			) );
		}

		/*
		 * The Header template
		 */
		function branding() {
			global $ss_settings, $knowledgepress, $meta;
			$meta = redux_post_meta( 'knowledgepress', get_the_ID() );

			echo '<div class="before-main-wrapper">';

			if( isset( $meta['page_header_video']['url'] ) && $meta['page_header_video']['url'] != '' ) {
				$url = ' data-vide-bg="' . $meta['page_header_video']['url'] . '"';
				$video = preg_replace('/\\.[^.\\s]{3,4}$/', '', $url);
			} else {
				$video = '';
			}

			echo '<div class="header-wrapper"' . $video . '" data-vide-options="posterType: none">';

			if ( isset( $meta['page_header_align'] ) && $meta['page_header_align'] != '' ) {
				if ( $meta['page_header_align'] == 1 ) {
					$align = 'header-center';
				} else {
					$align = 'header-left';
				}
			} else {
				if ( $ss_settings['header_align'] == 1 ) {
					$align = 'header-center';
				} else {
					$align = 'header-left';
				}
			}

			//if ( $ss_settings['site_style'] == 'wide' ) {
			//	echo '<div class="container ' . $align . '">';
			//}

			echo '<div class="container ' . $align . '">';
			echo '<div class="header-titles">';
			echo '<h1>' . pa_header_title() . '</h1>';
			if ( isset( $meta['page_header_subtitle'] ) && $meta['page_header_subtitle'] != '' ) {
				echo '<p>' . $meta['page_header_subtitle'] . '</p>';
			} elseif ( isset( $ss_settings['header_subtitle'] ) && $ss_settings['header_subtitle'] != '' ) {
				echo '<p>' . $ss_settings['header_subtitle'] . '</p>';
			}
			echo '</div >';	
			echo '</div >';		

			if ( isset( $meta['header_sidebar'] ) && $meta['header_sidebar'] != '' ) {
				$header_sidebar = $meta['header_sidebar'];
			} else {
				$header_sidebar = 'header-area';
			}

			if ( is_active_sidebar( $header_sidebar ) ) {
			echo '<div class="container ' . $align . '">';
			echo '<div class="header-sidebar">';
			dynamic_sidebar( $header_sidebar );
			echo '</div >';
			echo '</div >';		
			}

			//if ( $ss_settings['site_style'] == 'wide' ) {
			//	echo '</div >';
			//}

			echo '</div >';

			echo '</div >';
		}

		/*
		 * Any necessary extra CSS is generated here
		 */
		function css() {
			global $ss_settings, $knowledgepress, $meta;
			$meta = redux_post_meta( 'knowledgepress', get_the_ID() );
		
			if ( isset( $meta['header_color'] ) && $meta['header_color'] != '' ) {
				$cl = $meta['header_color'];
			} else {
				$cl = $ss_settings['header_color'];
			}

			//$header_margin_bottom = $ss_settings['header_margin_bottom'];

			//$rgb      = Shoestrap_Color::get_rgb( $bg, true );

			$element = 'body .before-main-wrapper .header-wrapper';

			$style  = $element . ' a, ' . $element . ' h1, ' . $element . ' h2, ' . $element . ' h3, ' . $element . ' h4, ' . $element . ' h5, ' . $element . ' h6  { color: ' . $cl . '; }';
			$style .= $element . '{ color: ' . $cl . ';';

			if ( isset( $meta['page_header_top_padding'] ) && $meta['page_header_top_padding'] > 0 ) {
				$top_padding = $meta['page_header_top_padding'];
			} else {
				$top_padding = $ss_settings['header_top_padding'];
			}
			$style .= 'padding-top:' . $top_padding . 'px;';

			if ( isset( $meta['page_header_bottom_padding'] ) && $meta['page_header_bottom_padding'] > 0 ) {
				$bottom_padding = $meta['page_header_bottom_padding'];
			} else {
				$bottom_padding = $ss_settings['header_bottom_padding'];
			}
			$style .= 'padding-bottom:' . $bottom_padding . 'px;';

			//if ( $header_margin_bottom > 0 ) {
			//	$style .= 'margin-bottom:' . $header_margin_bottom . 'px;';
			//}

			$style .= '}';

			if ( ( isset( $meta['page_header_bg']['background-color'] ) && $meta['page_header_bg']['background-color'] != '' ) || ( isset( $meta['page_header_bg']['background-image'] ) && $meta['page_header_bg']['background-image'] != '' ) ) {
			    if ( isset( $meta['page_header_bg']['background-color'] ) && $meta['page_header_bg']['background-color'] != '' ) {
			    	$style .= '.before-main-wrapper .header-wrapper:after { background-color: ' . $meta['page_header_bg']['background-color'] . '; }';
			    }
			    if ( isset( $meta['page_header_bg']['background-image'] ) && $meta['page_header_bg']['background-image'] != '' ) {
			    	$style .= '.before-main-wrapper .header-wrapper:after { background-image: url("' . $meta['page_header_bg']['background-image'] . '"); background-size: cover; background-repeat: no-repeat; }';
			    	$style .= '.before-main-wrapper .header-wrapper:after { background-position: ' . $meta['page_header_bg']['background-position'] . '; background-attachment: ' . $meta['page_header_bg']['background-attachment'] . '; }';
				}
			} else {
			    if ( isset( $ss_settings['header_bg']['background-color'] ) && $ss_settings['header_bg']['background-color'] != '' ) {
			        $style .= '.before-main-wrapper .header-wrapper:after { background-color: ' . $ss_settings['header_bg']['background-color'] . '; }';
			    }
			    if ( isset( $ss_settings['header_bg']['background-image'] ) && $ss_settings['header_bg']['background-image'] != '' ) {
			        $style .= '.before-main-wrapper .header-wrapper:after { background-image: url("' . $ss_settings['header_bg']['background-image'] . '"); background-size: cover; background-repeat: no-repeat; }';
			    	$style .= '.before-main-wrapper .header-wrapper:after { background-position: ' . $ss_settings['header_bg']['background-position'] . '; background-attachment: ' . $ss_settings['header_bg']['background-attachment'] . '; }';
			    }
			}

			if ( isset( $meta['page_header_overlay']['rgba'] ) && $meta['page_header_overlay']['rgba'] != '' ) {
			    $style .= '.header-wrapper:before { background-color: ' . $meta['page_header_overlay']['rgba'] . '; }';
			} else {
			    if ( isset( $ss_settings['header_overlay']['rgba'] ) && $ss_settings['header_overlay']['rgba'] != '' ) {
			        $style .= '.header-wrapper:before { background-color: ' . $ss_settings['header_overlay']['rgba'] . '; }';
			    }
			}


			wp_add_inline_style( 'knowledgepress_css', $style );
		}
	}
}
