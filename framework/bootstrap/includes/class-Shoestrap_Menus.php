<?php


if ( ! class_exists( 'Shoestrap_Menus' ) ) {

	/**
	* The "Menus" module
	*/
	class Shoestrap_Menus {

		function __construct() {
			global $ss_settings;

			add_filter( 'shoestrap_nav_class',        array( $this, 'nav_class' ) );
			add_action( 'shoestrap_inside_nav_begin', array( $this, 'navbar_pre_searchbox' ), 20 );
			add_filter( 'shoestrap_navbar_class',     array( $this, 'navbar_class' ) );
			add_action( 'wp_enqueue_scripts',         array( $this, 'css' ), 101 );
			add_filter( 'shoestrap_navbar_brand',     array( $this, 'navbar_brand' ) );
			add_filter( 'body_class',                 array( $this, 'navbar_body_class' ) );
			add_action( 'widgets_init',               array( $this, 'sidenav_widgets_init' ), 40 );
		
		}

		/**
		 * Modify the nav class.
		 */
		function nav_class() {
			global $ss_settings;

			if ( $ss_settings['navbar_nav_right'] == '1' ) {
				return 'navbar-nav nav navmenu-right';
			} else {
				return 'navbar-nav nav nav-primary-left';
			}
		}


		/*
		 * The template for the primary navbar searchbox
		 */
		function navbar_pre_searchbox() {
			global $ss_settings;

			$show_searchbox = $ss_settings['navbar_search'];
			if ( $show_searchbox == '1' ) : ?>
				<form role="search" method="get" id="searchform" class="form-search navbar-form" action="<?php echo home_url('/'); ?>">
					<label class="hide" for="s"><?php _e('Search for:', 'knowledgepress'); ?></label>
					<input type="text" value="<?php if (is_search()) { echo get_search_query(); } ?>" name="s" id="s" class="form-control search-query" placeholder="<?php echo esc_attr($ss_settings['navbar_search_placeholder']); ?>">
				</form>
			<?php elseif ( $show_searchbox == '2' ) : ?>
				<form role="search" method="get" id="searchform" class="form-search navbar-form" action="<?php echo home_url('/'); ?>">
					<label class="hide" for="s"><?php _e('Search for:', 'knowledgepress'); ?></label>
					<input type="text" value="<?php if (is_search()) { echo get_search_query(); } ?>" name="s" id="autocomplete-ajax" class="searchajax form-control search-query" placeholder="<?php echo esc_attr($ss_settings['navbar_search_placeholder']); ?>">
				</form>
				<script> _url = '<?php echo admin_url("admin-ajax.php"); ?>';</script>		
			<?php endif;
		}

		/**
		 * Modify the navbar class.
		 */
		public static function navbar_class( $navbar = 'main') {
			global $ss_settings;

			$fixed    = $ss_settings['navbar_fixed'];
			$defaults = 'navbar navbar-default topnavbar';

			if ( $fixed != 1 ) {
				$class = ' navbar-static-top';
			} else {
				$class = ' navbar-fixed-top';
			}

			$class = $defaults . $class;

			return $class;
		}

		/**
		 * Add some CSS for the navigations when needed.
		 */
		function css() {
			global $ss_settings;

			$style = '';

			// Side navbar
		    if ( $ss_settings['side_toggle_fixed'] ) {
		        $style .= '.navbar-default .navbar-toggle {position:fixed;}';
		    } else {
		        $style .= '.navbar-default .navbar-toggle {position:absolute;}';
		    }
		    if ( $ss_settings['side_toggle_on'] ) {
		        $style .= '.navbar-default .navbar-toggle {display:block;}';
		    }
		    $side_link_regular = $ss_settings['side_link']['regular'];
		    if ( !empty( $side_link_regular ) ) {
				$style .= '.offcanvas, .offcanvas a, .offcanvas .widget, .offcanvas caption, .offcanvas .navmenu-nav > li > a, .offcanvas .inline-social a, .offcanvas .side-navbar .widget h3 { color: ' . $side_link_regular . '; }';
				$style .= '.offcanvas .navmenu-nav > .dropdown > a .caret { border-top-color: ' . $side_link_regular . '; border-bottom-color: ' . $side_link_regular . '; }';
				$style .= '.offcanvas .has-button .navmenu-btn { border-color: ' . $side_link_regular . '; }';
		    }
		    $side_link_hover = $ss_settings['side_link']['hover'];
		    if ( !empty( $side_link_hover ) ) {
				$style .= '.offcanvas .widget a:hover, .offcanvas .navmenu-nav > li > a:hover, .offcanvas .inline-social a:hover { color: ' . $side_link_hover . '; }';
				$style .= '.offcanvas .navmenu-nav > .dropdown > a:hover .caret, .offcanvas .navmenu-nav > .open > a .caret, .offcanvas .navmenu-nav > .open > a:hover .caret, .offcanvas .navmenu-nav > .open > a:focus .caret { border-top-color: ' . $side_link_hover . '; border-bottom-color: ' . $side_link_hover . '; }';
				$style .= '.offcanvas .navmenu-nav > .active > a .caret, .offcanvas .navmenu-nav > .active > a:hover .caret, .offcanvas .navmenu-nav > .active > a:focus .caret { border-top-color: ' . $side_link_hover . '; border-bottom-color: ' . $side_link_hover . '; }';
				$style .= '.offcanvas .navmenu-nav > .active > a, .offcanvas .navmenu-nav > .active > a:hover, .offcanvas .navmenu-nav > .active > a:focus { color: ' . $side_link_hover . '; }';
				$style .= '.offcanvas .navmenu-nav > .open > a, .offcanvas .navmenu-nav > .open > a:hover, .offcanvas .navmenu-nav > .open > a:focus { color: ' . $side_link_hover . '; }';
				$style .= '.offcanvas .has-button .navmenu-btn:hover { border-color: ' . $side_link_hover . '; }';
		    }



			// Navbar navigation
		    $style .= '
		    .navbar .navbar-nav > li > a, .navbar,
		    .navbar .inline-social a {
		      color: ' . $ss_settings['link_navbar']['regular'] . ';
		    }
		    .navbar-default .navbar-toggle .icon-bar {
		      background-color: ' . $ss_settings['link_navbar']['regular'] . ';
		    }
		    .navbar-default .navbar-toggle:hover .icon-bar {
		      background-color: ' . $ss_settings['link_navbar']['hover'] . ';
		    }
		    .navbar .navbar-nav > li > a:hover,
		    .navbar .navbar-nav > .active > a,
		    .navbar .navbar-nav > .active > a:hover,
		    .navbar .navbar-nav > .active > a:focus,
		    .navbar .navbar-nav > li > a:focus, .navbar .navbar-nav > .open > a, .navbar .navbar-nav > .open > a:hover, .navbar .navbar-nav > .open > a:focus,
		    .navbar .inline-social a:hover {
		      color: ' . $ss_settings['link_navbar']['hover'] . ';
		    }
		    .navbar .dropdown-menu li a {
		      color: ' . $ss_settings['link_navbar']['regular'] . ';
		    }
		    .navbar .dropdown-menu > .active > a,
		    .navbar .dropdown-menu > li > a:hover,
		    .navbar .dropdown-menu > li > a:focus {
		      background: ' . $ss_settings['dropdown_hover_bg'] . ';
		      color: ' . $ss_settings['dropdown_hover_color'] . ';
		    }';

		    // Navbar search
		    if ( $ss_settings['navbar_search_right'] ) {
		        $style .= '.navbar-form {float: right; margin-right: 10px;}';
		    } else {
		        $style .= '.navbar-form {float: left; margin-left: 40px;}';
		    }
		    $style .= '
		    .navbar-form .form-control {
		      color: ' . $ss_settings['link_navbar']['regular'] . ';
		      font-size: ' . $ss_settings['font_navbar']['font-size'] . ';
		      width: ' . $ss_settings['navbar_search_width'] . 'px;
		    }
		    .navbar-form:before, .navbar .inline-social i {
		        font-size: ' . $ss_settings['font_navbar']['font-size'] . ';
		    }
		    .navbar-form input::-webkit-input-placeholder {
		      color: ' . $ss_settings['link_navbar']['regular'] . ';
		      font-size: ' . $ss_settings['font_navbar']['font-size'] . ';

		    }
		    .navbar-form input:-moz-placeholder {
		      color: ' . $ss_settings['link_navbar']['regular'] . ';
		      font-size: ' . $ss_settings['font_navbar']['font-size'] . ';

		    }
		    .navbar-form input::-moz-placeholder {
		      color: ' . $ss_settings['link_navbar']['regular'] . ';
		      font-size: ' . $ss_settings['font_navbar']['font-size'] . ';

		    }
		    .navbar-form input:-ms-input-placeholder {
		      color: ' . $ss_settings['link_navbar']['regular'] . ';
		      font-size: ' . $ss_settings['font_navbar']['font-size'] . ';

		    }';

			wp_add_inline_style( 'knowledgepress_css', $style );
		}

		/**
		 * get the navbar branding options (if the branding module exists)
		 * and then add the appropriate logo or sitename.
		 */
		function navbar_brand() {
			global $ss_settings, $ss_framework;

			$logo           = $ss_settings['logo'];
			$branding_class = ! empty( $logo['url'] ) ? 'logo' : 'text';

			$branding  = '<a class="navbar-brand ' . $branding_class . '" href="' . home_url('/') . '">';
			$branding .= ! empty( $logo['url'] ) ? $ss_framework->logo() : get_bloginfo( 'name' );
			$branding .= '</a>';

			return $branding;
		}

		/**
		 * Add and remove body_class() classes
		 */
		function navbar_body_class( $classes ) {
			global $ss_settings;

			// Add 'top-navbar' or 'bottom-navabr' class if using Bootstrap's Navbar
			// Used to add styling to account for the WordPress admin bar
			if ( $ss_settings['navbar_fixed'] == 1 ) {
				$classes[] = 'top-navbar';
			}

			return $classes;
		}

		/**
		 * Register sidebars and widgets
		 */
		function sidenav_widgets_init() {
			register_sidebar( array(
				'name'          => __( 'Side Navbar', 'knowledgepress' ),
				'id'            => 'side-navbar',
				'description'   => __( 'This widget area will show up in your off canvas right side navigation.', 'knowledgepress' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3>',
				'after_title'   => '</h3>',
			) );
		}

	}
}
