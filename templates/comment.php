<?php

global $ss_framework;

echo get_avatar( $comment, $size = '64' );

echo '<div class="media-body">';
	echo '<h4 class="media-heading">' . get_comment_author_link() . '</h4>';
	echo '<time><a href="' . htmlspecialchars( get_comment_link( $comment->comment_ID ) ) . '">';
		printf( __( '%1$s', 'knowledgepress' ), get_comment_date() );
	echo '</a></time>';

	edit_comment_link( __( '(Edit)', 'knowledgepress' ), '', '' );

	if ( $comment->comment_approved == '0' ) {
		echo $ss_framework->alert( 'info', __( 'Your comment is awaiting moderation.', 'knowledgepress' ) );
	}

	comment_text();
	comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) );