<?php ss_get_template_part( 'templates/head' ); ?>
<body <?php body_class(); ?>>
    <script> _url = '<?php echo home_url(); ?>';</script>
<a href="#content" class="sr-only"><?php _e( 'Skip to main content', 'knowledgepress' ); ?></a>
<?php 
global $ss_framework, $post, $knowledgepress, $meta;
$meta = redux_post_meta( 'knowledgepress', get_the_ID() );
?>

	<!--[if lt IE 8]>
		<?php echo $ss_framework->alert( 'warning', __(' You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'knowledgepress' ) ); ?>
	<![endif]-->

	<?php do_action( 'get_header' ); ?>

    <?php get_template_part('templates/offcanvas'); ?>

	<?php do_action( 'shoestrap_pre_top_bar' ); ?>

	<?php ss_get_template_part( 'templates/top-bar' ); ?>

	<?php do_action( 'shoestrap_pre_wrap' ); ?>
	
	<?php echo $ss_framework->open_container( 'div', 'wrap-main-section', 'wrap main-section' ); ?>

		<?php do_action( 'shoestrap_pre_content' ); ?>

		<div id="content" class="content">
			<?php echo $ss_framework->open_row( 'div', null, 'bg' ); ?>

				<?php do_action( 'shoestrap_pre_main' ); ?>

				<main class="main <?php shoestrap_section_class( 'main', true ); ?>" <?php if ( is_home() ) { echo 'id="home-blog"'; } ?> role="main">
					<?php include shoestrap_template_path(); ?>
				</main><!-- /.main -->

				<?php do_action( 'shoestrap_after_main' ); ?>

				<?php if ( shoestrap_display_primary_sidebar() ) : ?>
					<aside id="sidebar-primary" class="sidebar <?php shoestrap_section_class( 'primary', true ); ?>" role="complementary">
						<?php if ( ! has_action( 'shoestrap_sidebar_override' ) ) {
							include shoestrap_sidebar_path();
						} else {
							do_action( 'shoestrap_sidebar_override' );
						} ?>
					</aside><!-- /.sidebar -->
				<?php endif; ?>

				<?php do_action( 'shoestrap_post_main' ); ?>

				<?php if ( shoestrap_display_secondary_sidebar() ) : ?>
					<aside id="sidebar-secondary" class="sidebar secondary <?php shoestrap_section_class( 'secondary', true ); ?>" role="complementary">
						<?php
						if ( isset( $meta['page_secondary_sidebar'] ) && $meta['page_secondary_sidebar'] != '' ) {
							dynamic_sidebar($meta['page_secondary_sidebar']); 
						} else {
							dynamic_sidebar( 'sidebar-secondary' );
						}
						?>
					</aside><!-- /.sidebar -->
				<?php endif; ?>
				<?php echo $ss_framework->clearfix(); ?>
			<?php echo $ss_framework->close_row( 'div' ); ?>
		</div><!-- /.content -->
		<?php do_action( 'shoestrap_after_content' ); ?>
	<?php echo $ss_framework->close_container( 'div' ); ?><!-- /.wrap -->
	<?php

	do_action( 'shoestrap_pre_footer' );

	if ( ! has_action( 'shoestrap_footer_override' ) ) {
		ss_get_template_part( 'templates/footer' );
	} else {
		do_action( 'shoestrap_footer_override' );
	}

	do_action( 'shoestrap_after_footer' );

	wp_footer();

	?>
</body>
</html>
