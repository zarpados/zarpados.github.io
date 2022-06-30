<?php


if ( ! class_exists( 'Shoestrap_Options' ) ) {

	class Shoestrap_Options {

		public $args     = array();
		public $sections = array();
		public $theme;
		public $ReduxFramework;

		public function __construct() {

			if ( ! class_exists( 'ReduxFramework' ) ) {
				return;
			}

			// This is needed. Bah WordPress bugs.  ;)
			if (  true == Redux_Helpers::isTheme( __FILE__ ) ) {
				$this->initSettings();
			} else {
				add_action( 'plugins_loaded', array( $this, 'initSettings' ) );
			}
		}

		public function initSettings() {

			// Set the default arguments
			$this->setArguments();

			// Create the sections and fields
			$this->setSections();

			if ( ! isset( $this->args['opt_name'] ) ) { // No errors please
				return;
			}

			// If Redux is running as a plugin, this will remove the demo notice and links
			add_action( 'redux/loaded', array( $this, 'remove_demo' ) );

			$this->ReduxFramework = new ReduxFramework( $this->sections, $this->args );
		}

		public function setSections() {

			global $redux;

			$settings = get_option( SHOESTRAP_OPT_NAME );

			// General Settings
			$this->sections[] = array(
				'title' => __( 'General', 'knowledgepress' ),
				'icon'  => 'el-icon-home',
				'fields'  => apply_filters( 'shoestrap_module_general_options_modifier', array(
					array(
						'title'       => __( 'Logo', 'knowledgepress' ),
						'desc'        => __( 'Upload a logo image using the media uploader, or define the URL directly.', 'knowledgepress' ),
						'id'          => 'logo',
						'default'     => '',
						'type'        => 'media',
					),
					array(
						'title'       => __( 'Favicon', 'knowledgepress' ),
						'desc'        => __( 'Upload a favicon image using the media uploader, or define the URL directly.', 'knowledgepress' ),
						'id'          => 'favicon',
						'default'     => '',
						'type'        => 'media',
					),
					array(
						'title'       => __( 'Apple Icon', 'knowledgepress' ),
						'desc'        => __( 'This will create icons for Apple iPhone ( 57px x 57px ), Apple iPhone Retina Version ( 114px x 114px ), Apple iPad ( 72px x 72px ) and Apple iPad Retina ( 144px x 144px ). Please note upload image should be at least 144px x 144px.', 'knowledgepress' ),
						'id'          => 'apple_icon',
						'default'     => '',
						'type'        => 'media',
					),
				) ),
			);

			$std = array(
					array(
						'title'     => __( 'Site Width', 'knowledgepress' ),
						'desc'      => __( 'Select the default site layout.', 'knowledgepress' ),
						'id'        => 'site_style',
						'default'   => 'wide',
						'type'      => 'select',
						'options'   => array(
							//'static'  => __( 'Static (Non-Responsive)', 'knowledgepress' ),
							'wide'    => __( 'Fixed Width', 'knowledgepress' ),
							//'boxed'   => __( 'Boxed', 'knowledgepress' ),
							'fluid'   => __( 'Fluid Width', 'knowledgepress' ),
						),
						'compiler'  => true,
					),
					array(
						'title'     => __( 'Layout', 'knowledgepress' ),
						'desc'      => __( 'Select main content and sidebar arrangement. Choose between 1, 2 or 3 column layout.', 'knowledgepress' ),
						'id'        => 'layout',
						'default'   => 1,
						'type'      => 'image_select',
						'options'   => array(
							0 => ReduxFramework::$_url . '/assets/img/1c.png',
							1 => ReduxFramework::$_url . '/assets/img/2cr.png',
							2 => ReduxFramework::$_url . '/assets/img/2cl.png',
							3 => ReduxFramework::$_url . '/assets/img/3cl.png',
							4 => ReduxFramework::$_url . '/assets/img/3cr.png',
							5 => ReduxFramework::$_url . '/assets/img/3cm.png',
						)
					),
					array(
						'title'     => __( 'Primary Sidebar Width', 'knowledgepress' ),
						'desc'      => __( 'Select the width of the Primary Sidebar. Please note that the values represent grid columns. The total width of the page is 12 columns, so selecting 4 here will make the primary sidebar to have a width of 1/3 ( 4/12 ) of the total page width.', 'knowledgepress' ),
						'id'        => 'layout_primary_width',
						'type'      => 'button_set',
						'options'   => array(
							'1' => '1 Column',
							'2' => '2 Columns',
							'3' => '3 Columns',
							'4' => '4 Columns',
							'5' => '5 Columns'
						),
						'default' => '4'
					),
					array(
						'title'     => __( 'Secondary Sidebar Width', 'knowledgepress' ),
						'desc'      => __( 'Select the width of the Secondary Sidebar. Please note that the values represent grid columns. The total width of the page is 12 columns, so selecting 4 here will make the secondary sidebar to have a width of 1/3 ( 4/12 ) of the total page width.', 'knowledgepress' ),
						'id'        => 'layout_secondary_width',
						'type'      => 'button_set',
						'options'   => array(
							'1' => '1 Column',
							'2' => '2 Columns',
							'3' => '3 Columns',
							'4' => '4 Columns',
							'5' => '5 Columns'
						),
						'default' => '3'
					),
					array(
						'title'     => __( 'Body Top Margin', 'knowledgepress' ),
						'desc'      => __( 'Select the top margin of body element in pixels.', 'knowledgepress' ),
						'id'        => 'body_margin_top',
						'default'   => 0,
						'min'       => 0,
						'step'      => 1,
						'max'       => 200,
						'edit'      => 1,
						'type'      => 'slider',
					),
					array(
						'title'     => __( 'Custom Layouts per Post Type', 'knowledgepress' ),
						'desc'      => __( 'Set a default layout for each post type on your site.', 'knowledgepress' ),
						'id'        => 'cpt_layout_toggle',
						'default'   => 0,
						'type'      => 'switch',
					)
			);

			// Layout Settings
			$post_types = get_post_types( array( 'public' => true ), 'names' );
			$layout = isset( $ss_settings['layout'] ) ? $ss_settings['layout'] : 1;
			$layout_ppt_fields = array();
			foreach ( $post_types as $post_type ) {
				$layout_ppt_fields[] = array(
					'title'     => $post_type . ' ' . __( 'Layout', 'knowledgepress' ),
					'desc'      => __( 'Override your default stylings. Choose between 1, 2 or 3 column layout.', 'knowledgepress' ),
					'id'        => $post_type . '_layout',
					'default'   => $layout,
					'type'      => 'image_select',
					'required'  => array( 'cpt_layout_toggle','=',array( '1' ) ),
					'options'   => array(
						0         => ReduxFramework::$_url . '/assets/img/1c.png',
						1         => ReduxFramework::$_url . '/assets/img/2cr.png',
						2         => ReduxFramework::$_url . '/assets/img/2cl.png',
						3         => ReduxFramework::$_url . '/assets/img/3cl.png',
						4         => ReduxFramework::$_url . '/assets/img/3cr.png',
						5         => ReduxFramework::$_url . '/assets/img/3cm.png',
					)
				);
			}

			$this->sections[] = array(
				'title'       => __( 'Layout', 'knowledgepress' ),
				'icon'        => 'el-icon-screen',
				'description' => '<p>In this area you can select your site\'s layout, the width of your sidebars, as well as other, more advanced options.</p>',
				'fields'  => apply_filters( 'shoestrap_module_layout_advanced_options_modifier', array_merge($std, $layout_ppt_fields) )
			);

			// Menus Settings
			$this->sections[] = array(
				'title' => __( 'Menu', 'knowledgepress' ),
				'icon'  => 'el-icon-minus',
				'fields'  => apply_filters( 'shoestrap_module_menus_options_modifier', array(
					array(
						'title'       => __( 'Type of NavBar', 'knowledgepress' ),
						'desc'        => __( 'Choose the type of Navbar you want. Off completely hides the navbar.', 'knowledgepress' ),
						'id'          => 'navbar_toggle',
						'default'     => 'normal',
						'options'     => array(
							'none'    => __( 'Off', 'knowledgepress' ),
							'normal'  => __( 'Normal', 'knowledgepress' ),
							'full'    => __( 'Full-Width', 'knowledgepress' ),
						),
						'type'        => 'button_set'
					),
					array(
						'title'       => __( 'NavBar Positioning', 'knowledgepress' ),
						'desc'        => __( 'Using this option you can set the navbar to be fixed to top, fixed to bottom or normal. When you\'re using one of the \'fixed\' options, the navbar will stay fixed on the top or bottom of the page.', 'knowledgepress' ),
						'id'          => 'navbar_fixed',
						'default'     => 0,
						'on'          => __( 'Fixed', 'knowledgepress' ),
						'off'         => __( 'Scroll', 'knowledgepress' ),
						'type'        => 'switch'
					),
					array(
						'title'       => __( 'Display social links in the NavBar.', 'knowledgepress' ),
						'desc'        => __( 'Display social links in the NavBar. These can be setup in the \'Social\' section on the left.', 'knowledgepress' ),
						'id'          => 'navbar_social',
						'default'     => 0,
						'type'        => 'switch'
					),
					array(
						'title'       => __( 'Display social links as a Dropdown list or an Inline list.', 'knowledgepress' ),
						'desc'        => __( 'How to display social links.', 'knowledgepress' ),
						'id'          => 'navbar_social_style',
						'default'     => 0,
						'on'          => __( 'Inline', 'knowledgepress' ),
						'off'         => __( 'Dropdown', 'knowledgepress' ),
						'type'        => 'switch',
						'required'    => array('navbar_social','=',array('1')),
					),
	                array(
	                    'id' => 'navbar_search',
	                    'type' => 'button_set',
						'title'       => __( 'Search form on the NavBar', 'knowledgepress' ),
						'desc'        => __( 'Display a search form in the NavBar.', 'knowledgepress' ),
	                    'options'   => array(
	                        '0' => 'Disabled',
	                        '1' => 'WP Search',
	                        '2' => 'Live Search',
	                    ),
	                    'default'     => 0,
	                ),
					array(
						'title'       => __( 'Float Search to the right', 'knowledgepress' ),
						'id'          => 'navbar_search_right',
						'default'     => 1,
						'type'        => 'switch',
						'required'    => array('navbar_search','=',array('1','2')),
					),
					array(
						'title'       => __( 'NavBar Search Width', 'knowledgepress' ),
						'id'          => 'navbar_search_width',
						'default'     => 300,
						'min'         => 150,
						'step'        => 10,
						'max'         => 500,
						'type'        => 'slider',
						'required'    => array('navbar_search','=',array('1','2')),
						//'output'      => array('width' => '.navbar-form input')
					),
                    array(
                        'title'     => __( 'Placeholder', 'knowledgepress' ),
                        'id'        => 'navbar_search_placeholder',
                        'default'   => 'Search Knowledge Base',
                        'type'      => 'text',
                        'validate' => 'no_html',
						'required'    => array('navbar_search','=',array('1','2')),
                    ),
					array(
						'title'       => __( 'Float NavBar menu to the right', 'knowledgepress' ),
						'desc'        => __( 'Floats the primary navigation to the right.', 'knowledgepress' ),
						'id'          => 'navbar_nav_right',
						'default'     => 0,
						'type'        => 'switch'
					),






                    array(
                        'id'       => 'navbar_user',
                        'type'     => 'switch',
                        'title'    => __( 'Enable User Navigation', 'knowledgepress' ),
                        'default'  => true,
                    ),
                    array(
                        'id'       => 'navbar_login',
                        'type'     => 'switch',
                        'title'    => __( 'Login Link', 'knowledgepress' ),
                        'default'  => true,
                        'required' => array('navbar_user','=','1')
                    ),
                    array(
                        'id'       => 'navbar_register',
                        'type'     => 'switch',
                        'title'    => __( 'Register Link', 'knowledgepress' ),
                        'default'  => false,
                        'required' => array('navbar_user','=','1')
                    ),
                    array(
                        'id'    => 'navbar_custom_page',
                        'title' => __( 'Custom Page', 'knowledgepress'),
                        'type'  => 'select',
                        'data'  => 'post',
                        'args'  => array(
                            'post_type' => 'page',
                            'posts_per_page' => -1,
                        ),
                        'required' => array('navbar_user','=','1'),
                        'default'  => '',
                    ),

				) ),
			);

			// Menus Styling Settings
			$this->sections[] = array(
				'title' => __( 'Menus Styling', 'knowledgepress' ),
				'icon'  => 'el-icon-chevron-right',
				'subsection' => true,
				'fields'  => apply_filters( 'shoestrap_module_menus_styling_options_modifier', array(
					array(
						'title'       => __( 'NavBar Background Color', 'knowledgepress' ),
						'desc'        => __( 'Pick a background color for the NavBar.', 'knowledgepress' ),
						'id'          => 'navbar_bg',
						'default'     => '#ffffff',
						'transparent' => false,
						'type'        => 'color',
						'output'      => array('background-color' => '.navbar, .navbar .dropdown-menu, .navbar-default .navbar-toggle, .navbar-default .navbar-toggle:hover, .navbar-default .navbar-toggle:focus')
					),
					array(
						'title'       => __( 'Navbar Font', 'knowledgepress' ),
						'desc'        => __( 'The font used in navbars.', 'knowledgepress' ),
						'id'          => 'font_navbar',
						'color'		  => false,
						'line-height' => false,
						'text-align'  => false,
						'default'     => array(
							'font-family' => 'Muli, sans-serif',
							'font-size'   => '14px',
							'google'      => 'true',
							'font-weight' =>  '400'
						),
						'preview'     => array(
							'text'    => __( 'This is my preview text!', 'knowledgepress' ), //this is the text from preview box
							'size'    => 30 //this is the text size from preview box
						),
						'type'        => 'typography',
						'output'      => '.navbar, .dropdown-menu',
					),
					array(
					    'id'       => 'link_navbar',
					    'type'     => 'link_color',
					    'title'    => __('Navbar Link Color', 'knowledgepress'),
					    'desc'     => __('The link colors used in navbars.', 'knowledgepress'),
					    'active'   => false,
					    'default'  => array(
					        'regular'  => '#000000',
					        'hover'    => '#03A9F4',
					    )
					),
					array(
					    'id'       => 'dropdown_hover_color',
					    'type'     => 'color',
					    'title'    => __('Dropdown Hover Color', 'knowledgepress'),
					    'default'  => '#ffffff',
					),	
					array(
					    'id'       => 'dropdown_hover_bg',
					    'type'     => 'color',
					    'title'    => __('Dropdown Hover Background', 'knowledgepress'),
					    'default'  => '#03A9F4',
					),					
					array(
						'title'       => __( 'Branding Font', 'knowledgepress' ),
						'desc'        => __( 'The branding font for your site.', 'knowledgepress' ),
						'id'          => 'font_brand',
						'line-height' => false,
						'text-align'  => false,
						'default'     => array(
							'font-family' => 'Arial, Helvetica, sans-serif',
							'font-size'   => 18,
							'google'      => 'false',
							'color'       => '#000000',
						),
						'preview'     => array(
							'text'    => __( 'This is my preview text!', 'knowledgepress' ), //this is the text from preview box
							'size'    => 30 //this is the text size from preview box
						),
						'type'        => 'typography',
						'output'      => '.navbar-brand.text, .navbar-brand.text:hover',
					),
				) ),
			);

			// Side Nav Settings
			$this->sections[] = array(
				'title' => __( 'Side Navigation', 'knowledgepress'),
				'icon'  => 'el-icon-list el-rotate-180',
				'fields'  => array(
					array(
						'title'       => __( 'Background Color', 'knowledgepress' ),
						'desc'        => __( 'Select background color for sidebar navigation.', 'knowledgepress' ),
						'id'          => 'side_bg',
						'default'     => '#0c0c0c',
						'compiler'    => false,
						'transparent' => false,
						'type'        => 'color',
						'output'      => array('background-color' => '.navmenu-inverse'),
					),
					array(
					    'id'       => 'side_link',
					    'type'     => 'link_color',
					    'title'    => __('Link Color', 'knowledgepress'),
					    'desc'     => __('The link colors used in side navigation.', 'knowledgepress'),
					    'active'   => false,
					    'default'  => array(
					        'regular'  => '#bfbfbf',
					        'hover'    => '#ffffff',
					    ),
					),
					array(
						'title'     => __( 'Always On', 'knowledgepress' ),
						'desc'        => __( 'Enable to display navigation toggle button on all devices. Disable to disply only on mobile devices.', 'knowledgepress' ),
						'id'        => 'side_toggle_on',
						'default'   => 1,
						'type'      => 'switch',
					),
					array(
						'title'     => __( 'Fixed Toggle Button', 'knowledgepress' ),
						'desc'        => __( 'Enable fixed position for navigation toggle button. Disable to scroll toggle button with navbar.', 'knowledgepress' ),
						'id'        => 'side_toggle_fixed',
						'default'   => 1,
						'type'      => 'switch',
					),
					array(
						'title'     => __( 'Display Admin Button', 'knowledgepress' ),
						'id'        => 'side_admin_btn',
						'default'   => 1,
						'type'      => 'switch',
					),
					array(
						'title'     => __( 'Display Login Button', 'knowledgepress' ),
						'id'        => 'side_login_btn',
						'default'   => 1,
						'type'      => 'switch',
					),
					array(
						'title'     => __( 'Display Register Button', 'knowledgepress' ),
						'id'        => 'side_register_btn',
						'default'   => 1,
						'type'      => 'switch',
					),
					array(
						'title'     => __( 'Display social links', 'knowledgepress' ),
						'id'        => 'side_social',
						'default'   => 1,
						'type'      => 'switch',
					),
				),
			);

			// Header Settings
			$this->sections[] = array(
				'title' => __( 'Header', 'knowledgepress'),
				'icon'  => 'el-icon-eye-open',
				'fields'  => apply_filters( 'shoestrap_module_header_options_modifier', array(
	                array(
	                    'id' => 'header_subtitle', 
	                    'type' => 'text', 
	                    'title' => __('Subtitle', 'knowledgepress' ),
	                    'desc' => __("Enter header subtitle.", 'knowledgepress' ),
	                    "default" => "",
	                ),
					array(
						'title'       => __( 'Header Background', 'knowledgepress' ),
						'desc'        => __( 'Specify the default background for site header.', 'knowledgepress' ),
						'id'          => 'header_bg',
						'type'        => 'background',
						'background-repeat' => false,
						'background-size' => false,
						'transparent' => false,
						'default'     => array(
							'background-color' => '#03A9F4'
						),
					),
                    array(
                        'id'        => 'header_overlay',
                        'type'      => 'color_rgba',
                        'title'     => 'Overlay',
                        'transparent'   => false,
                        'validate' => 'colorrgba',
					    'default'   => array(
					        'color'     => '',
					        'alpha'     => 0
				    	),
                    ),
	                array(
	                    'id' => 'header_align',
	                    'type' => 'button_set',
						'title'       => __( 'Content Align', 'knowledgepress' ),
						'desc'        => __( 'Select header content alignment.', 'knowledgepress' ),
	                    'options'   => array(
	                        '0' => 'Left',
	                        '1' => 'Center',
	                    ),
	                    'default'     => 0,
	                ),
					array(
						'title'       => __( 'Header Text Color', 'knowledgepress' ),
						'desc'        => __( 'Select the text color for your header.', 'knowledgepress' ),
						'id'          => 'header_color',
						'default'     => '#ffffff',
						'transparent' => false,
						'type'        => 'color',
					),
					array(
						'title'       => __( 'Header Top Padding', 'knowledgepress' ),
						'desc'        => __( 'Select the top padding of header in pixels.', 'knowledgepress' ),
						'id'          => 'header_top_padding',
						'default'     => 25,
						'min'         => 0,
						'step'        => 1,
						'max'         => 700,
						'type'        => 'slider'
					),
					array(
						'title'       => __( 'Header Bottom Padding', 'knowledgepress' ),
						'desc'        => __( 'Select the bottom padding of header in pixels.', 'knowledgepress' ),
						'id'          => 'header_bottom_padding',
						'compiler'    => true,
						'default'     => 25,
						'min'         => 0,
						'step'        => 1,
						'max'         => 700,
						'type'        => 'slider'
					),
				) ),
			);

			// Articles Settings
			$this->sections[] = array(
				'title'   => __( 'Articles', 'knowledgepress' ),
				'icon'    => 'el-icon-align-justify',
				'fields'  => apply_filters( 'shoestrap_module_blog_modifier', array(
					array(
						'title'     => __( 'Show Breadcrumbs', 'knowledgepress' ),
						'desc'      => __( 'Display Breadcrumbs.', 'knowledgepress' ),
						'id'        => 'breadcrumbs',
						'default'   => 0,
						'type'      => 'switch',
					),
					array(
						'id'          => 'shoestrap_entry_meta_config',
						'title'       => __( 'Activate and order Post Meta elements', 'knowledgepress' ),
						'options'     => array(
							'post-format'		=> 'Post Format',
							'tags'    			=> 'Tags',
							'date'    			=> 'Date',
							'category'			=> 'Category',
							'author'  			=> 'Author',
							'comment-count'		=> 'Comments',
							'sticky'  			=> 'Sticky'
						),
						'type'        => 'sortable',
						'mode'        => 'checkbox'
					),
					array(
						'title'     => __( 'Switch Date Meta in time_diff mode', 'knowledgepress' ),
						'desc'      => __( 'Replace Date Meta element by displaying the difference between post creation timestamp and current timestamp.', 'knowledgepress' ),
						'id'        => 'date_meta_format',
						'default'   => 0,
						'type'      => 'switch',
					),
                    array(
                        'id'       => 'reset_all_views',
                        'type'     => 'button_set',
                        'title'    => __( 'Reset All Article Views', 'knowledgepress' ),
                        'desc'     => __( 'Reset views on all articles! Click reset button and Save Changes.', 'knowledgepress' ),
                        'options'  => array(
                            '1' => 'Reset All Views'
                        ),
                        'default'  => '0',
                    ),
                    array(
                        'id' => 'reorder',
                        'type' => 'switch',
                        'title' => __('Reorder', 'knowledgepress' ), 
                        'desc' => __('Enable drag and drop reordering under Posts>>All Posts and Posts>>Categories.', 'knowledgepress' ),
                        "default"       => 1,
                        'on' => 'Enabled',
                        'off' => 'Disabled',
                    ),
				) ),
			);

			// Archive page
			$this->sections[] = array(
				'title'   => __( 'Archive Page', 'knowledgepress' ),
				'icon'    => 'el-icon-chevron-right',
				'subsection' => true,
				'fields'  => apply_filters( 'shoestrap_module_archive_page_modifier', array(
					array(
						'title'     => __( 'Post Format Icons', 'knowledgepress' ),
						'desc'      => __( 'Display post format icon in titles on post archives.', 'knowledgepress' ),
						'id'        => 'archive_post_format_icons',
						'default'   => 1,
						'type'      => 'switch',
					),
					array(
						'title'     => __( 'Archives Display Mode', 'knowledgepress' ),
						'desc'      => __( 'Display the excerpt or the full post on post archives.', 'knowledgepress' ),
						'id'        => 'blog_post_mode',
						'default'   => 'excerpt',
						'type'      => 'button_set',
						'options'   => array(
							'excerpt' => __( 'Excerpt', 'knowledgepress' ),
							'full'    => __( 'Full Post', 'knowledgepress' ),
						),
					),
					array(
						'title'     => __( 'Post excerpt length', 'knowledgepress' ),
						'desc'      => __( 'Choose how many words should be used for post excerpt.', 'knowledgepress' ),
						'id'        => 'post_excerpt_length',
						'default'   => 40,
						'min'       => 10,
						'step'      => 1,
						'max'       => 1000,
						'edit'      => 1,
						'type'      => 'slider',
						'required'  => array( 'blog_post_mode','=',array( 'excerpt' ) ),
					),
					array(
						'title'     => __( '"More" text', 'knowledgepress' ),
						'desc'      => __( 'Text to display in case of excerpt too long.', 'knowledgepress' ),
						'id'        => 'post_excerpt_link_text',
						'default'   => __( 'Continued', 'knowledgepress' ),
						'type'      => 'text',
						'required'  => array( 'blog_post_mode','=',array( 'excerpt' ) ),
					),
					array(
						'title'     => __( 'Archive Meta', 'knowledgepress' ),
						'desc'      => __( 'Display meta elements on archive page', 'knowledgepress' ),
						'id'        => 'archive_meta',
						'default'   => 0,
						'type'      => 'switch',
					),

				) ),
			);

			// Single page
			$this->sections[] = array(
				'title'   => __( 'Single Page', 'knowledgepress' ),
				'icon'    => 'el-icon-chevron-right',
				'subsection' => true,
				'fields'  => apply_filters( 'shoestrap_module_single_page_modifier', array(
                    array(
                        'id' => 'related_posts',
                        'type' => 'switch',
                        'title' => __('Related Posts', 'knowledgepress' ), 
                        'desc' => __('Display related posts on single page.', 'knowledgepress' ),
                        'default' => '1',
        				'on' => 'Enabled',
        				'off' => 'Disabled',
                    ),
                    array(
                        'id' => 'post_author',
                        'type' => 'switch',
                        'title' => __('Post Author', 'knowledgepress' ), 
                        'desc' => __('Display author info box on post single page.', 'knowledgepress' ),
                        'default' => '1',
        				'on' => 'Enabled',
        				'off' => 'Disabled',
                    ),
				) ),
			);

			// Blog Settings
			$screen_large_desktop = isset( $ss_settings['screen_large_desktop'] ) ? filter_var( $ss_settings['screen_large_desktop'], FILTER_SANITIZE_NUMBER_INT ) : 1260;

			$post_types = get_post_types( array( 'public' => true ), 'names' );
			$post_type_options  = array();
			$post_type_defaults = array();

			foreach ( $post_types as $post_type ) {
				$post_type_options[$post_type]  = $post_type;
				$post_type_defaults[$post_type] = 0;
			}

			$this->sections[] = array(
				'title'   => __( 'Featured Images', 'knowledgepress' ),
				'icon'    => 'el-icon-chevron-right',
				'subsection' => true,
				'fields'  => apply_filters( 'shoestrap_module_featured_images_modifier', array(
					array(
						'title'     => __( 'Featured Media on Archives', 'knowledgepress' ),
						'desc'      => __( 'Display featured media on post archives ( such as categories, tags, month view etc ).', 'knowledgepress' ),
						'id'        => 'feat_img_archive',
						'default'   => 1,
						'type'      => 'switch',
					),
					array(
						'title'     => __( 'Width of Featured Images on Archives', 'knowledgepress' ),
						'desc'      => __( 'Set dimensions of featured Images on Archives.', 'knowledgepress' ),
						'id'        => 'feat_img_archive_custom_toggle',
						'default'   => 0,
						'required'  => array( 'feat_img_archive','=',array( '1' ) ),
						'off'       => __( 'Full Width', 'knowledgepress' ),
						'on'        => __( 'Custom Dimensions', 'knowledgepress' ),
						'type'      => 'switch',
					),
					array(
						'title'     => __( 'Archives Featured Image Custom Width', 'knowledgepress' ),
						'desc'      => __( 'Select the width of your featured images on single posts.', 'knowledgepress' ),
						'id'        => 'feat_img_archive_width',
						'default'   => 550,
						'min'       => 100,
						'step'      => 1,
						'max'       => $screen_large_desktop,
						'required'  => array(
							array( 'feat_img_archive', '=', 1 ),
							array( 'feat_img_archive_custom_toggle', '=', 1 ),
						),
						'edit'      => 1,
						'type'      => 'slider'
					),
					array(
						'title'     => __( 'Archives Featured Image Custom Height', 'knowledgepress' ),
						'desc'      => __( 'Select the height of your featured images on post archives.', 'knowledgepress' ),
						'id'        => 'feat_img_archive_height',
						'default'   => 300,
						'min'       => 50,
						'step'      => 1,
						'edit'      => 1,
						'max'       => $screen_large_desktop,
						'required'  => array( 'feat_img_archive', '=', 1 ),
						'type'      => 'slider'
					),
					array(
						'title'     => __( 'Featured Media on Single Posts', 'knowledgepress' ),
						'desc'      => __( 'Display featured media on posts.', 'knowledgepress' ),
						'id'        => 'feat_img_post',
						'default'   => 1,
						'type'      => 'switch',
					),
					array(
						'title'     => __( 'Width of Featured Images on Single Posts', 'knowledgepress' ),
						'desc'      => __( 'Set dimensions of featured Images on Posts.', 'knowledgepress' ),
						'id'        => 'feat_img_post_custom_toggle',
						'default'   => 0,
						'off'       => __( 'Full Width', 'knowledgepress' ),
						'on'        => __( 'Custom Dimensions', 'knowledgepress' ),
						'type'      => 'switch',
						'required'  => array( 'feat_img_post', '=', 1 ),
					),
					array(
						'title'     => __( 'Single Posts Featured Image Custom Width', 'knowledgepress' ),
						'desc'      => __( 'Select the width of your featured images on single posts.', 'knowledgepress' ),
						'id'        => 'feat_img_post_width',
						'default'   => 550,
						'min'       => 100,
						'step'      => 1,
						'max'       => $screen_large_desktop,
						'edit'      => 1,
						'required'  => array(
							array( 'feat_img_post', '=', 1 ),
							array( 'feat_img_post_custom_toggle', '=', 1 ),
						),
						'type'      => 'slider'
					),
					array(
						'title'     => __( 'Single Posts Featured Image Custom Height', 'knowledgepress' ),
						'desc'      => __( 'Select the height of your featured images on single posts.', 'knowledgepress' ),
						'id'        => 'feat_img_post_height',
						'default'   => 330,
						'min'       => 50,
						'step'      => 1,
						'max'       => $screen_large_desktop,
						'edit'      => 1,
						'required'  => array( 'feat_img_post', '=', 1 ),
						'type'      => 'slider'
					),
				) ),
			);

			// Footer Settings
			$this->sections[] = array(
				'title'   => __( 'Footer', 'knowledgepress' ),
				'icon' => 'el-icon-website',
				'fields'  => apply_filters( 'shoestrap_module_footer_options_modifier', array(
					array(
						'title'     => __( 'CTA Footer', 'knowledgepress' ),
						'desc'      => __( 'Display call to action section above footer.', 'knowledgepress' ),
						'id'        => 'footer_cta',
						'default'   => 0,
						'type'      => 'switch',
					),
					array(
					    'id'               => 'footer_cta_content',
					    'type'             => 'editor',
					    'title'            => __('CTA Content', 'redux-framework-demo'), 
					    'default'          => "<h3 style='text-align: center;'>Can't find what you're looking for? <a href='#'>Contact Us</a></h3>",
					    'args'   => array(
					        'teeny'            => true,
					        'textarea_rows'    => 10
					    ),
                        'required' => array('footer_cta','=','1'),
					),
                    array(
                        'id'    => 'footer_cta_pages',
                        'title' => __( 'CTA Display On', 'knowledgepress'),
						'desc'  => __( 'Select on which pages to display footer call to action.', 'knowledgepress' ),
                        'type'  => 'select',
                        'options'  => array(
                            'knowledgebase' 	=> 'Knowledge Base Template',
                            'category' 			=> 'Article Category',
                            'single' 			=> 'Single Article',
                            'vc' 				=> 'Visual Composer Template',
                            'page' 				=> 'Default Page Template',
                        ),
                        'required' => array('footer_cta','=','1'),
                        'multi'	   => true,
                        'default'  => array('single'),
                    ),
					array(
						'title'       => __( 'CTA Background Color', 'knowledgepress' ),
						'desc'        => __( 'Select the background color for footer call to action.', 'knowledgepress' ),
						'id'          => 'footer_cta_background',
						'default'     => '#f5f5f5',
						'transparent' => false,
						'type'        => 'color',
                        'mode' 		  => 'background-color',
						'output'	  => '.footer-cta',
                        'required' => array('footer_cta','=','1'),
					),
					array(
						'title'       => __( 'CTA Color', 'knowledgepress' ),
						'desc'        => __( 'Select content color for footer call to action.', 'knowledgepress' ),
						'id'          => 'footer_cta_color',
						'default'     => '#181818',
						'transparent' => false,
						'type'        => 'color',
						'output'	  => '.footer-cta',
                        'required' => array('footer_cta','=','1'),
					),
					array(
						'title'       => __( 'Sidebar Background Color', 'knowledgepress' ),
						'desc'        => __( 'Select the background color for your footer sidebar.', 'knowledgepress' ),
						'id'          => 'footer_background',
						'default'     => '#111111',
						'transparent' => false,
						'type'        => 'color'
					),
					array(
						'title'       => __( 'Sidebar Titles Color', 'knowledgepress' ),
						'desc'        => __( 'Select the title text color for your footer widgets.', 'knowledgepress' ),
						'id'          => 'footer_color_title',
						'default'     => '#FFFFFF',
						'transparent' => false,
						'type'        => 'color',
						'output'	  => '.content-info h3'
					),
					array(
						'title'       => __( 'Footer Text/Link Color', 'knowledgepress' ),
						'desc'        => __( 'Select the text & link color for your footer.', 'knowledgepress' ),
						'id'          => 'footer_color',
						'default'     => '#8C8989',
						'transparent' => false,
						'type'        => 'color',
						'output'	  => '.content-info, .content-info a'
					),
					array(
						'title'       => __( 'Footer Link Hover Color', 'knowledgepress' ),
						'desc'        => __( 'Select the link hover color for your footer.', 'knowledgepress' ),
						'id'          => 'footer_color_hover',
						'default'     => '#FFFFFF',
						'transparent' => false,
						'type'        => 'color',
						'output'	  => '.content-info a:hover'
					),
					array(
						'title'       => __( 'Footer Text', 'knowledgepress' ),
						'desc'        => __( 'The text that will be displayed in your footer. You can use [year] and [sitename] and they will be replaced appropriately. Default: &copy; [year] [sitename]', 'knowledgepress' ),
						'id'          => 'footer_text',
						'default'     => '&copy; [year] [sitename]',
						'type'        => 'textarea'
					),
					array(
						'title'       => 'Footer Border',
						'desc'        => 'Select the border options for your Footer',
						'id'          => 'footer_border',
						'type'        => 'border',
						'all'         => false,
						'left'        => false,
						'bottom'      => false,
						'right'       => false,
						'default'     => array(
							'border-top'      => '0',
							'border-bottom'   => '0',
							'border-style'    => 'solid',
							'border-color'    => '#4B4C4D',
						),
					),
					array(
						'title'       => __( 'Footer Top Margin', 'knowledgepress' ),
						'desc'        => __( 'Select the top margin of footer in pixels.', 'knowledgepress' ),
						'id'          => 'footer_top_margin',
						'default'     => 50,
						'min'         => 0,
						'max'         => 200,
						'type'        => 'slider',
					),
					array(
						'title'       => __( 'Show social icons in footer', 'knowledgepress' ),
						'desc'        => __( 'Show social icons in the footer.', 'knowledgepress' ),
						'id'          => 'footer_social_toggle',
						'default'     => 1,
						'type'        => 'switch',
					),
				) ),
			);

			// Header Settings
			$this->sections[] = array(
				'title' => __( 'Search', 'knowledgepress'),
				'icon'  => 'el-icon-search',
				'fields'  => apply_filters( 'shoestrap_module_header_options_modifier', array(
                    array(
                        'id' => 'live_search_in',
                        'type' => 'select',
                        'title' => __('Search Titles / Content', 'knowledgepress' ), 
                        'desc' => __('Search in post titles only or post titles and content.', 'knowledgepress' ),
                        'options' => array(
                            '1' => 'Titles Only',
                            '2' => 'Titles and Content'
                        ), 
                        'default' => '2',
                    ),
                    array(
                        'id'=>'search_post_types',
                        'type' => 'select',
                        'data' => 'post_type',
                        'multi' => true,
                        'title' => __('Post Types', 'knowledgepress'), 
                        'desc' => __('Select post types to display in live search', 'knowledgepress'),
                        'default' => 'post'
                    ),
					array(
						'title'       => __( 'Disable Widget Live Search', 'knowledgepress' ),
						'desc'        => __( 'Optionally disable live search in "KP Live Search" widget.', 'knowledgepress' ),
						'id'          => 'disable_widget_live_search',
						'default'     => 0,
						'type'        => 'switch'
					),
                    array(
                        'id'       => 'search_analytics',
                        'type'     => 'switch',
                        'title'    => __( 'Search Analytics', 'knowledgepress' ),
                        'desc'     => __( 'Enable to save user search data for analytics', 'knowledgepress' ),
                        'default'  => '1'
                    ),
		            array(
		                'id'       => 'top_searches',
		                'type'     => 'switch',
		                'title'    => __( 'Pupular Searched Terms', 'redux-framework-demo' ),
		                'desc'     => __( 'Display top searched terms under the widget search field.', 'redux-framework-demo' ),
		                'default'  => 1,
		                'required'    => array('search_analytics','=','1'),
		            ),
		            array(
		                'id'       => 'top_searches_title',
		                'type'     => 'text',
		                'title'    => __( 'Title', 'redux-framework-demo' ),
		                'required'    => array('top_searches','=','1'),
		                'default'  => 'Popular searches:',
		            ),
		            array(
		                'id' => 'top_searches_period',
		                'type' => 'button_set',
		                'title'       => __( 'Search Period', 'shoestrap' ),
		                'desc'        => __( 'Display top searches for a selected time period.', 'shoestrap' ),
		                'required'    => array('top_searches','=','1'),
		                'options'   => array(
		                    '1' => '1 Day',
		                    '7' => '1 Week',
		                    '30' => '1 Month',
		                    '999999' => 'All Time',
		                ),
		                'default'  => '999999',
		            ),
		            array(
		                'id'      => 'top_searches_terms',
		                'type'    => 'spinner',
		                'title'   => __( 'Number of Terms', 'redux-framework-demo' ),
		                'desc'    => __( 'Select how many search terms to display.', 'redux-framework-demo' ),
		                'required'    => array('top_searches','=','1'),
		                'default' => '4',
		                'min'     => '1',
		                'step'    => '1',
		                'max'     => '20',
		            ),
                    array(
                        'id'       => 'reset_all_searches',
                        'type'     => 'button_set',
                        'title'    => __( 'Reset All Article Searches', 'knowledgepress' ),
                        'desc'     => __( 'Reset searche terms on all articles! Click reset button and Save Changes.', 'knowledgepress' ),
                        'options'  => array(
                            '1' => 'Reset All Searches'
                        ),
                        'default'  => '0',
		                'required'    => array('search_analytics','=','1'),
                    ),
				) ),
			);


			// Voting
			$this->sections[] = array(
				'title'   => __( 'Voting', 'knowledgepress' ),
				'icon'    => 'el-icon-thumbs-up',
				'fields'  => apply_filters( 'shoestrap_module_voting_options_modifier', array(
                    array(
                        'id'       => 'article_voting',
                        'type'     => 'button_set',
                        'title'    => __( 'Article Voting', 'knowledgepress' ),
                        'options'  => array(
                            '0' => 'Disabled',
                            '1' => 'Public Voting',
                            '2' => 'Logged In Users Only'
                        ),
                        'default'  => '1'
                    ),
					array(
						'title'     => __( 'Question', 'knowledgepress' ),
						'id'        => 'vote_question',
						'default'   => 'Was this article helpful to you?',
						'type'      => 'text',
						'required'  => array( 'article_voting','!=',array( '0' ) ),
					),
					array(
						'title'     => __( 'Confirmation', 'knowledgepress' ),
						'id'        => 'vote_confirmation',
						'default'   => 'Thank you for your feedback!',
						'type'      => 'text',
						'required'  => array( 'article_voting','!=',array( '0' ) ),
					),
                    array(
                        'id'       => 'reset_all_votes',
                        'type'     => 'button_set',
                        'title'    => __( 'Reset All Article Votes', 'knowledgepress' ),
                        'desc'     => __( 'Reset votes on all articles! Click reset button and Save Changes.', 'knowledgepress' ),
                        'options'  => array(
                            '1' => 'Reset All Votes'
                        ),
                        'default'  => '0',
						'required'  => array( 'article_voting','!=',array( '0' ) ),
                    ),
				) ),
			);

			// Views
			/*
			$this->sections[] = array(
				'title'   => __( 'Views', 'knowledgepress' ),
				'icon'    => 'el-icon-eye-open',
				'fields'  => apply_filters( 'shoestrap_module_views_options_modifier', array(
                    array(
                        'id'       => 'article_views',
                        'type'     => 'switch',
                        'title'    => __( 'Article views', 'knowledgepress' ),
                        'desc'     => __( 'Enable to save article views data for analytics', 'knowledgepress' ),
                        'default'  => '1'
                    ),
				) ),
			);
			*/
		
			// Colors Settings
			$this->sections[] = array(
				'title'   => __( 'Colors', 'knowledgepress' ),
				'icon'    => 'el-icon-tint',
				'fields'  => apply_filters( 'shoestrap_module_branding_options_modifier', array(
					array(
						'title'       => __( 'Page Background Color', 'knowledgepress' ),
						'desc'        => __( 'Select a background color for your site.', 'knowledgepress' ),
						'id'          => 'html_bg',
						'default'     => '#ffffff',
						'compiler'    => true,
						'transparent' => false,
						'type'        => 'color',
						'output'      => 'body',
                        'mode' => 'background-color',
					),
					array(
						'title'       => __( 'Brand Colors: Primary', 'knowledgepress' ),
						'desc'        => __( 'Select your primary branding color. Also referred to as an accent color. This will affect various areas of your site, including the color of your primary buttons, link color, the background of some elements and many more.', 'knowledgepress' ),
						'id'          => 'color_brand_primary',
						'default'     => '#03A9F4',
						'compiler'    => true,
						'transparent' => false,
						'type'        => 'color'
					),
					array(
						'title'       => __( 'Brand Colors: Primary Hover', 'knowledgepress' ),
						'desc'        => __( 'Select your primary branding hover color. Also referred to as an accent color. This will affect various areas of your site, including the color of your primary buttons, link color, the background of some elements and many more.', 'knowledgepress' ),
						'id'          => 'color_brand_primary_hover',
						'default'     => '#222222',
						'compiler'    => false,
						'transparent' => false,
						'type'        => 'color',
					),
				) ),
			);

			// Typography Settings
			$this->sections[] = array(
				'title'   => __( 'Typography', 'knowledgepress' ),
				'icon'    => 'el-icon-font',
				'fields'  => apply_filters( 'shoestrap_module_typography_options_modifier', array(
					array(
						'title'     => __( 'Base Font', 'knowledgepress' ),
						'desc'      => __( 'The main font for your site.', 'knowledgepress' ),
						'id'        => 'font_base',
						'compiler'  => false,
						'units'     => 'px',
						'default'   => array(
							'font-family'   => 'Muli',
							'font-size'     => '16px',
							'line-height'   => '30px',
							'google'        => 'true',
							'weight'        => 'inherit',
							'color'         => '#333333',
							'font-style'    => 400,
							'update_weekly' => true // Enable to force updates of Google Fonts to be weekly
						),
						'preview'   => array(
							'text'        => __( 'This is my preview text!', 'knowledgepress' ), //this is the text from preview box
							'font-size'   => '30px' //this is the text size from preview box
						),
						'type'      => 'typography',
						'output'    => 'body',
					),
					array(
						'title'     => __( 'H Tag Font', 'knowledgepress' ),
						'desc'      => __( 'The main font for your site.', 'knowledgepress' ),
						'id'        => 'font_h',
						'compiler'  => false,
						'line-height' => false,
						'font-size' => false,
						'units'     => 'px',
						'default'   => array(
							'font-family' => 'Muli',
							'font-size'   => '28px',
							'color'       => $settings['font_base']['color'],
							'google'      => 'true',
							'font-style'  => 400,

						),
						'preview'   => array(
							'text'        => __( 'This is my preview text!', 'knowledgepress' ), //this is the text from preview box
							'font-size'   => '30px' //this is the text size from preview box
						),
						'type'      => 'typography',
						'output'    => 'h1, .h1, h2, .h2, h3, .h3, h4, .h4, h5, .h5, h6, .h6',
					),
					array(
						'id'        => 'font_title',
						'title'     => __( 'Title Font', 'knowledgepress' ),
						'desc'      => __( 'Page title font.', 'knowledgepress' ),
						'compiler'  => false,
						'color'		=> false,
						'line-height' => false,
						'letter-spacing' => true,
						'units'     => 'px',
						'default'   => array(
							'font-family' => 'Raleway',
							'font-size'   => '30px',
							'letter-spacing' => '2px',
							'google'      => 'true',
							'font-weight' => 300,
						),
						'preview'   => array(
							'text'        => __( 'This is my preview text!', 'knowledgepress' ), //this is the text from preview box
							'font-size'   => '30px' //this is the text size from preview box
						),
						'type'      => 'typography',
						'output'    => '.header-titles h1',
					),
				) ),
			);

			// Social Settings
			$this->sections[] = array(
				'title'     => __( 'Social', 'knowledgepress' ),
				'icon'      => 'el-icon-group',
				'fields'  => apply_filters( 'shoestrap_module_socials_options_modifier', array(
					array(
						'id'        => 'social_sharing_help_1',
						'title'     => __( 'Social Sharing', 'knowledgepress' ),
						'type'      => 'info'
					),
					array(
						'title'     => __( 'Button Text', 'knowledgepress' ),
						'desc'      => __( 'Select the text for the social sharing button.', 'knowledgepress' ),
						'id'        => 'social_sharing_text',
						'default'   => 'Share',
						'type'      => 'text'
					),
					array(
						'title'     => __( 'Button Location', 'knowledgepress' ),
						'desc'      => __( 'Select between NONE, TOP, BOTTOM & BOTH. For archives, "BOTH" fallbacks to "BOTTOM" only.', 'knowledgepress' ),
						'id'        => 'social_sharing_location',
						'default'   => 'top',
						'type'      => 'select',
						'options'   => array(
							'none'    =>'None',
							'top'     =>'Top',
							'bottom'  =>'Bottom',
							'both'    =>'Both',
						)
					),
					array(
						'title'     => __( 'Button Styling', 'knowledgepress' ),
						'desc'      => __( 'Select between standard Bootstrap\'s button classes', 'knowledgepress' ),
						'id'        => 'social_sharing_button_class',
						'default'   => 'default',
						'type'      => 'select',
						'options'   => array(
							'default'    => 'Default',
							'primary'    => 'Primary',
							'success'    => 'Success',
							'warning'    => 'Warning',
							'danger'     => 'Danger',
						)
					),
					array(
						'title'     => __( 'Show in Posts Archives', 'knowledgepress' ),
						'desc'      => __( 'Show the sharing button in posts archives.', 'knowledgepress' ),
						'id'        => 'social_sharing_archives',
						'default'   => '',
						'type'      => 'switch'
					),
					array(
						'title'     => __( 'Show in Single Post', 'knowledgepress' ),
						'desc'      => __( 'Show the sharing button in single post.', 'knowledgepress' ),
						'id'        => 'social_sharing_single_post',
						'default'   => '1',
						'type'      => 'switch'
					),
					array(
						'title'     => __( 'Show in Single Page', 'knowledgepress' ),
						'desc'      => __( 'Show the sharing button in single page.', 'knowledgepress' ),
						'id'        => 'social_sharing_single_page',
						'default'   => '1',
						'type'      => 'switch'
					),
					array(
						'id'        => 'share_networks',
						'type'      => 'checkbox',
						'title'     => __( 'Social Share Networks', 'knowledgepress' ),
						'desc'      => __( 'Select the Social Networks you want to enable for social shares', 'knowledgepress' ),

						'options'   => array(
							'fb'    => __( 'Facebook', 'knowledgepress' ),
							'gp'    => __( 'Google+', 'knowledgepress' ),
							'li'    => __( 'LinkedIn', 'knowledgepress' ),
							'pi'    => __( 'Pinterest', 'knowledgepress' ),
							'tu'    => __( 'Tumblr', 'knowledgepress' ),
							'tw'    => __( 'Twitter', 'knowledgepress' ),
							'em'    => __( 'Email', 'knowledgepress' ),
						)
					),
				) ),
			);

			// Social Settings
			$this->sections[] = array(
				'title'     => __( 'Social Links', 'knowledgepress' ),
				'icon'      => 'el-icon-chevron-right',
				'subsection' => true,
				'fields'  => apply_filters( 'shoestrap_module_social_links_options_modifier', array(
					array(
						'id'        => 'social_sharing_help_3',
						'title'     => __( 'Social Links used in Menus && Footer. Enter full profile URL. To remove, just leave blank.', 'knowledgepress' ),
						'type'      => 'info'
					),
					array(
						'title'     => __( 'Bitbucket', 'knowledgepress' ),
						'id'        => 'bitbucket_link',
						'validate'  => 'url',
						'default'   => '',
						'type'      => 'text'
					),
					array(
						'title'     => __( 'Digg', 'knowledgepress' ),
						'id'        => 'digg_link',
						'validate'  => 'url',
						'default'   => '',
						'type'      => 'text'
					),
					array(
						'title'     => __( 'Dribbble', 'knowledgepress' ),
						'id'        => 'dribbble_link',
						'validate'  => 'url',
						'default'   => '',
						'type'      => 'text'
					),
					array(
						'title'     => __( 'Facebook', 'knowledgepress' ),
						'id'        => 'facebook_link',
						'validate'  => 'url',
						'default'   => '',
						'type'      => 'text'
					),
					array(
						'title'     => __( 'Flickr', 'knowledgepress' ),
						'id'        => 'flickr_link',
						'validate'  => 'url',
						'default'   => '',
						'type'      => 'text'
					),
					array(
						'title'     => __( 'GitHub', 'knowledgepress' ),
						'id'        => 'github_link',
						'validate'  => 'url',
						'default'   => '',
						'type'      => 'text'
					),
					array(
						'title'     => __( 'Google+', 'knowledgepress' ),
						'id'        => 'google_plus_link',
						'validate'  => 'url',
						'default'   => '',
						'type'      => 'text'
					),
					array(
						'title'     => __( 'Instagram', 'knowledgepress' ),
						'id'        => 'instagram_link',
						'validate'  => 'url',
						'default'   => '',
						'type'      => 'text'
					),
					array(
						'title'     => __( 'LinkedIn', 'knowledgepress' ),
						'id'        => 'linkedin_link',
						'validate'  => 'url',
						'default'   => '',
						'type'      => 'text'
					),
					array(
						'title'     => __( 'Pinterest', 'knowledgepress' ),
						'id'        => 'pinterest_link',
						'validate'  => 'url',
						'default'   => '',
						'type'      => 'text'
					),
					array(
						'title'     => __( 'Reddit', 'knowledgepress' ),
						'id'        => 'reddit_link',
						'validate'  => 'url',
						'default'   => '',
						'type'      => 'text'
					),
					array(
						'title'     => __( 'RSS', 'knowledgepress' ),
						'id'        => 'rss_link',
						'validate'  => 'url',
						'default'   => '',
						'type'      => 'text'
					),
					array(
						'title'     => __( 'Skype', 'knowledgepress' ),
						'id'        => 'skype_link',
						'validate'  => 'url',
						'default'   => '',
						'type'      => 'text'
					),
					array(
						'title'     => __( 'SoundCloud', 'knowledgepress' ),
						'id'        => 'soundcloud_link',
						'validate'  => 'url',
						'default'   => '',
						'type'      => 'text'
					),
					array(
						'title'     => __( 'Tumblr', 'knowledgepress' ),
						'id'        => 'tumblr_link',
						'validate'  => 'url',
						'default'   => '',
						'type'      => 'text'
					),
					array(
						'title'     => __( 'Twitter', 'knowledgepress' ),
						'id'        => 'twitter_link',
						'validate'  => 'url',
						'default'   => '',
						'type'      => 'text'
					),
					array(
						'title'     => __( 'Vimeo', 'knowledgepress' ),
						'id'        => 'vimeo_link',
						'validate'  => 'url',
						'default'   => '',
						'type'      => 'text'
					),
					array(
						'title'     => 'YouTube',
						'id'        => 'youtube_link',
						'validate'  => 'url',
						'default'   => '',
						'type'      => 'text'
					),
				) ),
			);

			// Advanced Settings
			$this->sections[] = array(
				'title'   => __( 'Advanced', 'knowledgepress' ),
				'icon'    => 'el-icon-cogs',
				'fields'  => apply_filters( 'shoestrap_module_advanced_options_modifier', array(
					array(
						'title'     => __( 'Enable Retina mode', 'knowledgepress' ),
						'desc'      => __( 'By enabling your site\'s featured images will be retina ready. Requires images to be uploaded at 2x the typical size desired.', 'knowledgepress' ),
						'id'        => 'retina_toggle',
						'default'   => 1,
						'type'      => 'switch',
					),
					array(
						'title'     => __( 'Google Analytics ID', 'knowledgepress' ),
						'desc'      => __( 'Paste your Google Analytics ID here to enable analytics tracking. Only Universal Analytics properties. Your user ID should be in the form of UA-XXXXX-Y.', 'knowledgepress' ),
						'id'        => 'analytics_id',
						'default'   => '',
						'type'      => 'text',
					),
					array(
						'title'     => __( 'Border-Radius', 'knowledgepress' ),
						'desc'      => __( 'You can adjust the corner-radius of all elements in your site here. This will affect buttons, navbars, widgets and many more.', 'knowledgepress' ),
						'id'        => 'general_border_radius',
						'default'   => 1,
						'min'       => 0,
						'step'      => 1,
						'max'       => 50,
						'advanced'  => true,
						'compiler'  => true,
						'type'      => 'slider',
					),
					array(
						'title'     => __( 'Enable Nice Search', 'knowledgepress' ),
						'desc'      => __( 'Redirects /?s=query to /search/query/, convert %20 to +.', 'knowledgepress' ),
						'id'        => 'nice_search',
						'default'   => 1,
						'type'      => 'switch',
					),
					array(
						'title'     => __( 'Custom CSS', 'knowledgepress' ),
						'desc'      => __( 'You can write your custom CSS here. This code will appear in a script tag appended in the header section of the page.', 'knowledgepress' ),
						'id'        => 'user_css',
						'default'   => '',
						'type'      => 'ace_editor',
						'mode'      => 'css',
						'theme'     => 'monokai',
					),
					array(
						'title'     => __( 'Custom JS', 'knowledgepress' ),
						'desc'      => __( 'You can write your custom JavaScript/jQuery here. The code will be included in a script tag appended to the bottom of the page.', 'knowledgepress' ),
						'id'        => 'user_js',
						'default'   => '',
						'type'      => 'ace_editor',
						'mode'      => 'javascript',
						'theme'     => 'monokai',
					),
				) ),
			);
		}

		public function setArguments() {

			$theme = wp_get_theme(); // For use with some settings. Not necessary.

			$this->args = array(
				// TYPICAL -> Change these values as you need/desire
				'opt_name'          => SHOESTRAP_OPT_NAME,
				'display_name'      => $theme->get( 'Name' ),
				'display_version'   => $theme->get( 'Version' ),
				'menu_type'         => 'menu',
				'allow_sub_menu'    => true,
				'menu_title'        => __( 'KnowledgePress', 'knowledgepress'),
				'page_title'        => __('KnowledgePress Options', 'knowledgepress'),
				'global_variable'   => 'redux',

				'google_api_key'    => 'AIzaSyCDiOc36EIOmwdwspLG3LYwCg9avqC5YLs',

				'admin_bar'         => true,
				'dev_mode'          => false,
				'customizer'        => false,

				// OPTIONAL -> Give you extra features
				'page_priority'     => null,                    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
				'page_parent'       => 'themes.php',            // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
				'page_permissions'  => 'manage_options',        // Permissions needed to access the options panel.
				'menu_icon'         => '',                      // Specify a custom URL to an icon
				'last_tab'          => '',                      // Force your panel to always open to a specific tab (by id)
				'page_icon'         => 'icon-themes',           // Icon displayed in the admin panel next to your menu_title
				'page_slug'         => SHOESTRAP_OPT_NAME,
				'save_defaults'     => true,                    // On load save the defaults to DB before user clicks save or not
				'default_show'      => false,                   // If true, shows the default value next to each field that is not the default value.
				'default_mark'      => '',                      // What to print by the field's title if the value shown is default. Suggested: *
				'show_import_export' => true,                   // Shows the Import/Export panel when not used as a field.

				// CAREFUL -> These options are for advanced use only
				'transient_time'    => 60 * MINUTE_IN_SECONDS,
				'output'            => true,                    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
				'output_tag'        => true,                    // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
				// 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.

				// FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
				'database'              => '', // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
				'system_info'           => false, // REMOVE

				'forced_edd_license' => true,

			);

		}

		// Remove the demo link and the notice of integrated demo from the redux-framework plugin
		function remove_demo() {

			// Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
			if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
				remove_filter( 'plugin_row_meta', array( ReduxFrameworkPlugin::instance(), 'plugin_metalinks' ), null, 2 );

				// Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
				remove_action( 'admin_notices', array( ReduxFrameworkPlugin::instance(), 'admin_notices' ) );
			}
		}
	}
}

function shoestrap_init_options(){
	global $ss_options;
	$ss_options = new Shoestrap_Options();
}
add_action( 'init', 'shoestrap_init_options' );



