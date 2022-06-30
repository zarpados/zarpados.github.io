<?php

global $ss_framework;

echo '<article '; post_class(); echo '>';

	do_action( 'shoestrap_in_article_top' );
	shoestrap_title_section( true, 'h2', true );

	do_action( 'pa_entry_media' );

	echo '<div class="entry-summary">';
		echo apply_filters( 'shoestrap_do_the_excerpt', get_the_excerpt() );
		echo $ss_framework->clearfix();
	echo '</div>';

	echo '<footer class="entry-footer">';
	do_action( 'shoestrap_entry_meta' );	
	do_action( 'shoestrap_entry_footer' );
	echo '</footer>';

	do_action( 'shoestrap_in_article_bottom' );

echo '</article>';
