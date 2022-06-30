<?php

global $ss_framework;

while ( have_posts() ) : the_post();
	the_content();
	echo $ss_framework->clearfix();
	do_action( 'shoestrap_page_after_content' );

	wp_link_pages( array( 'before' => '<nav class="pagination">', 'after' => '</nav>' ) );
endwhile;