<?php

do_action( 'shoestrap_index_begin' );

if ( ! have_posts() ) {
	echo '<div class="alert alert-warning">' . __( 'Sorry, no results were found.', 'knowledgepress' ) . '</div>';
	get_search_form();
}

if ( ! has_action( 'shoestrap_override_index_loop' ) ) {
	while ( have_posts() ) : the_post();
		do_action( 'shoestrap_in_loop_start' );

		if ( ! has_action( 'shoestrap_content_override' ) ) {
			ss_get_template_part( 'templates/content', get_post_format() );
		} else {
			do_action( 'shoestrap_content_override' );
		}

		do_action( 'shoestrap_in_loop_end' );
	endwhile;
} else {
	do_action( 'shoestrap_override_index_loop' );
}

do_action( 'shoestrap_index_end' );

echo shoestrap_pagination_toggler();
