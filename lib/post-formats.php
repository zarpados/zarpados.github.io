<?php

/*
 * Add the necessary actions and filters for post formats.
 */
function shoestrap_post_formats() {
	if ( is_singular() ) {

		// If the post format is set to "aside", don't display a title.
		// If the post format is set to "link", make the link into a button.
		if ( get_post_format() == 'aside' ) {
			add_filter( 'shoestrap_title_section', '__return_null', 20 );
		} elseif ( get_post_format() == 'gallery' ) {
			add_filter( 'shoestrap_title', 'shoestrap_post_format_gallery_title', 20 );
		} elseif ( get_post_format() == 'link' ) {
			add_filter( 'the_content', 'shoestrap_post_formats_link' );
			add_filter( 'shoestrap_title', 'shoestrap_post_format_link_title', 20 );
		} elseif ( get_post_format() == 'image' ) {
			add_filter( 'shoestrap_title', 'shoestrap_post_format_image_title', 20 );
		} elseif ( get_post_format() == 'quote' ) {
			add_filter( 'shoestrap_title', 'shoestrap_post_format_quote_title', 20 );
		} elseif ( get_post_format() == 'status' ) {
			add_filter( 'shoestrap_title', 'shoestrap_post_format_status_title', 20 );
		} elseif ( get_post_format() == 'video' ) {
			add_filter( 'shoestrap_title', 'shoestrap_post_format_video_title', 20 );
		} elseif ( get_post_format() == 'audio' ) {
			add_filter( 'shoestrap_title', 'shoestrap_post_format_audio_title', 20 );
		} elseif ( get_post_format() == 'chat' ) {
			add_filter( 'shoestrap_title', 'shoestrap_post_format_chat_title', 20 );
		} else {
			add_filter( 'shoestrap_title', 'shoestrap_post_format_standard_title', 20 );
		}
	}
}
add_action( 'wp', 'shoestrap_post_formats' );


/**
 * Add icons to post format titles (gallery)
 */
function shoestrap_post_format_gallery_title( $title ) {
	return '<i class="kp-file-text2"></i> ' . $title;
}

/**
 * Add icons to post format titles (link)
 */
function shoestrap_post_format_link_title( $title ) {
	return '<i class="kp-link2"></i> ' . $title;
}

/**
 * Add icons to post format titles (image)
 */
function shoestrap_post_format_image_title( $title ) {
	return '<i class="kp-image4"></i> ' . $title;
}

/**
 * Add icons to post format titles (quote)
 */
function shoestrap_post_format_quote_title( $title ) {
	return '<i class="kp-file-text2"></i> ' . $title;
}

/**
 * Add icons to post format titles (status)
 */
function shoestrap_post_format_status_title( $title ) {
	return '<i class="kp-file-text2"></i> ' . $title;
}

/**
 * Add icons to post format titles (video)
 */
function shoestrap_post_format_video_title( $title ) {
	return '<i class="kp-film3"></i> ' . $title;
}

/**
 * Add icons to post format titles (audio)
 */
function shoestrap_post_format_audio_title( $title ) {
	return '<i class="kp-volume-medium3"></i> ' . $title;
}

/**
 * Add icons to post format titles (chat)
 */
function shoestrap_post_format_chat_title( $title ) {
	return '<i class="kp-file-text2"></i> ' . $title;
}

/**
 * Add icons to post format titles (standard)
 */
function shoestrap_post_format_standard_title( $title ) {
	return '<i class="kp-file-text2"></i> ' . $title;
}

/*
 * If the post format is set to "link", make the link into a button.
 */
function shoestrap_post_formats_link( $content ) {
	global $ss_framework;

	return str_replace( '<a ', '<a class="' . $ss_framework->button_classes( 'primary', 'large' ) . '" ', $content );
}
