<?php

global $ss_framework, $ss_settings, $meta;

while ( have_posts() ) : the_post();

	echo '<article class="' . implode( ' ', get_post_class() ) . '">';
		do_action( 'shoestrap_single_top' );

		echo '<div class="entry-content">';
			do_action( 'shoestrap_single_pre_content' );
			do_action( 'pa_entry_media' );
			if ( isset( $meta['post_toc'] ) && $meta['post_toc'] ) {
				echo '<ul class="toc" data-toc=".entry-content" data-toc-headings="h2,h3,h4">';
				echo '<h5>' . __( 'Content', 'knowledgepress' ) . '</h5>';
				echo '</ul>';
			}
			the_content();
			echo $ss_framework->clearfix();
			do_action( 'shoestrap_single_after_content' );
		echo '</div>';

		echo '<footer class="entry-footer">';
			do_action( 'shoestrap_entry_meta' );
		echo '</footer>';

	    if ( $ss_settings['article_voting'] != '0' ) {
	      pa_article_voting();
	    }

	    if ( $ss_settings['related_posts'] ) {
	      get_template_part('templates/related');
	    } 

	    if ( $ss_settings['post_author'] ) {
	      get_template_part('templates/author');
	    } 

		// The comments section loaded when appropriate
		if ( post_type_supports( 'post', 'comments' ) ) {
			do_action( 'shoestrap_pre_comments' );

			if ( ! has_action( 'shoestrap_comments_override' ) ) {
				comments_template( '/templates/comments.php' );
			} else {
				do_action( 'shoestrap_comments_override' );
			}
			
			do_action( 'shoestrap_after_comments' );
		}

		do_action( 'shoestrap_in_article_bottom' );
	echo '</article>';
endwhile;
