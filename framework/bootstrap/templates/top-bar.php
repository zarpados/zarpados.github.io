<?php
global $ss_settings;
$navbar_toggle = $ss_settings['navbar_toggle'];
?>

<?php if ( $navbar_toggle != 'none' ) { ?>

	<header id="banner-header" class="banner <?php echo apply_filters( 'shoestrap_navbar_class', 'navbar navbar-default' ); ?>" role="banner">
		<div class="<?php echo apply_filters( 'shoestrap_navbar_container_class', 'container' ); ?>">
			<div class="navbar-header">
				<?php echo apply_filters( 'shoestrap_navbar_brand', '<a class="navbar-brand text" href="' . home_url('/') . '">' . get_bloginfo( 'name' ) . '</a>' ); ?>
			</div>
			<nav class="nav-main navbar-collapse collapse" role="navigation">

                <?php if (is_user_logged_in()) { ?>
                    <ul class="nav navbar-nav nav-user pull-right">
                        <li class="dropdown">
                            <?php 
                            $current_user = wp_get_current_user();
                            $current_user_name = $current_user->display_name;
                            ?>
                            <a href="#" data-toggle="dropdown"><?php echo get_avatar( get_current_user_id(), 30 ); ?><?php echo esc_attr($current_user_name); ?></a>
                            <ul class="dropdown-menu" role="menu">
                                <?php if ( current_user_can( 'publish_posts') ) { ?>
                                    <li><a href="<?php echo admin_url(); ?>"><?php _e('Admin', 'knowledgepress' ); ?></a></li>
                                <?php } ?>
                            	<?php do_action( 'shoestrap_user_menu' ); ?>
                                <?php if ( isset($ss_settings['navbar_custom_page']) && $ss_settings['navbar_custom_page'] != '' ) { ?>
                                    <li><a href="<?php echo get_permalink($ss_settings['navbar_custom_page']); ?>"><?php echo get_the_title( $ss_settings['navbar_custom_page'] ); ?></a></li>
                                <?php } ?>
                                <!--
                                <li><a href="<?php echo get_author_posts_url(get_current_user_id()); ?>"><?php _e('Profile', 'knowledgepress' ); ?></a></li>
                                -->
                                <li><a href="<?php echo wp_logout_url( home_url() ); ?>"><?php _e('Logout', 'knowledgepress' ); ?></a></li>
                            </ul>
                        </li>
                    </ul>
                <?php } else { ?>
                    <ul class="nav navbar-nav nav-user-links pull-right">
                        <?php if ($ss_settings['navbar_login']) { ?>
                            <li><a href="<?php echo wp_login_url(get_permalink() ); ?>" title="Login" class="navbar-link"><?php _e('Login', 'knowledgepress' ); ?></a></li>
                        <?php } ?>
                        <?php if ($ss_settings['navbar_register']) { ?>
                            <li><a href="<?php echo wp_registration_url(); ?>" title="Register" class="hidden-sm"><?php _e('Register', 'knowledgepress' ); ?></a></li>
                        <?php } ?>
                    </ul>
                <?php } ?>


				<?php do_action( 'shoestrap_inside_nav_begin' ); ?>
				<?php
				if ( has_nav_menu( 'primary_navigation' ) )
					wp_nav_menu( array( 'theme_location' => 'primary_navigation', 'menu_class' => apply_filters( 'shoestrap_nav_class', 'navbar-nav nav' ) ) );
				?>
				<?php
				?>
				<?php do_action( 'shoestrap_inside_nav_end' ); ?>

			</nav>
			
			<button type="button" class="navbar-toggle" data-recalc="false" data-toggle="offcanvas" data-target="#offcanvas" data-canvas="body">
			  <span class="sr-only">Toggle navigation</span>
			  <span class="icon-bar"></span>
			  <span class="icon-bar"></span>
			  <span class="icon-bar"></span>
			</button>
			<?php do_action( 'shoestrap_post_main_nav' ); ?>
		</div>
	</header>

	<?php do_action( 'shoestrap_do_navbar' ); ?>

<?php } ?>

