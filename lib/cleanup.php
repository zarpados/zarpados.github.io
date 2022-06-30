<?php
/**
 * Fix for empty search queries redirecting to home page
 *
 * @link http://wordpress.org/support/topic/blank-search-sends-you-to-the-homepage#post-1772565
 * @link http://core.trac.wordpress.org/ticket/11330
 */
function shoestrap_request_filter( $query_vars ) {
	if ( isset( $_GET['s'] ) && empty( $_GET['s'] ) ) {
		$query_vars['s'] = ' ';
	}

	return $query_vars;
}
add_filter( 'request', 'shoestrap_request_filter' );

/**
 * Tell WordPress to use searchform.php from the templates/ directory
 */
function shoestrap_get_search_form( $form ) {
	$form = '';
	ss_locate_template( '/templates/searchform.php', true, false );
	return $form;
}
add_filter( 'get_search_form', 'shoestrap_get_search_form' );


/**
 * Retrieve paginated link for archive post pages.
 *
 * Technically, the function can be used to create paginated link list for any
 * area. The 'base' argument is used to reference the url, which will be used to
 * create the paginated links. The 'format' argument is then used for replacing
 * the page number. It is however, most likely and by default, to be used on the
 * archive post pages.
 *
 * The 'type' argument controls format of the returned value. The default is
 * 'plain', which is just a string with the links separated by a newline
 * character. The other possible values are either 'array' or 'list'. The
 * 'array' value will return an array of the paginated link list to offer full
 * control of display. The 'list' value will place all of the paginated links in
 * an unordered HTML list.
 *
 * The 'total' argument is the total amount of pages and is an integer. The
 * 'current' argument is the current page number and is also an integer.
 *
 * An example of the 'base' argument is "http://example.com/all_posts.php%_%"
 * and the '%_%' is required. The '%_%' will be replaced by the contents of in
 * the 'format' argument. An example for the 'format' argument is "?page=%#%"
 * and the '%#%' is also required. The '%#%' will be replaced with the page
 * number.
 *
 * You can include the previous and next links in the list by setting the
 * 'prev_next' argument to true, which it is by default. You can set the
 * previous text, by using the 'prev_text' argument. You can set the next text
 * by setting the 'next_text' argument.
 *
 * If the 'show_all' argument is set to true, then it will show all of the pages
 * instead of a short list of the pages near the current page. By default, the
 * 'show_all' is set to false and controlled by the 'end_size' and 'mid_size'
 * arguments. The 'end_size' argument is how many numbers on either the start
 * and the end list edges, by default is 1. The 'mid_size' argument is how many
 * numbers to either side of current page, but not including current page.
 *
 * It is possible to add query vars to the link by using the 'add_args' argument
 * and see {@link add_query_arg()} for more information.
 *
 * @since 2.1.0
 *
 * @param string|array $args Optional. Override defaults.
 * @return array|string String of page links or array of page links.
 */
function shoestrap_paginate_links( $args = '' ) {
	global $ss_framework;

	$defaults = array(
		'base'         => '%_%', // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
		'format'       => '?page=%#%', // ?page=%#% : %#% is replaced by the page number
		'total'        => 1,
		'current'      => 0,
		'show_all'     => false,
		'prev_next'    => true,
		'prev_text'    => __( '&laquo; Previous', 'knowledgepress' ),
		'next_text'    => __( 'Next &raquo;', 'knowledgepress' ),
		'end_size'     => 1,
		'mid_size'     => 2,
		'type'         => 'plain',
		'add_args'     => false, // array of query args to add
		'add_fragment' => ''
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	// Who knows what else people pass in $args
	$total = (int) $total;
	if ( $total < 2 ) {
		return;
	}

	$current  = (int) $current;
	$end_size = 0  < (int) $end_size ? (int) $end_size : 1; // Out of bounds?  Make it the default.
	$mid_size = 0 <= (int) $mid_size ? (int) $mid_size : 2;
	$add_args = is_array($add_args) ? $add_args : false;
	$r = '';
	$page_links = array();
	$n = 0;
	$dots = false;

	if ( $prev_next && $current && 1 < $current ) {
		$link = str_replace( '%_%', 2 == $current ? '' : $format, $base );
		$link = str_replace( '%#%', $current - 1, $link );
		if ( $add_args ) {
			$link = esc_url( add_query_arg( $add_args, $link ) );
		}
		$link .= $add_fragment;
		$page_links[] = '<li><a class="prev page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $prev_text . '</a></li>';
	}
	for ( $n = 1; $n <= $total; $n++ ) {
		$n_display = number_format_i18n($n);
		if ( $n == $current ) {
			$page_links[] = "<li class='active current'><span class='page-numbers current'>$n_display</span></li>";
			$dots = true;
		} else {
			if ( $show_all || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) {
				$link = str_replace('%_%', 1 == $n ? '' : $format, $base);
				$link = str_replace('%#%', $n, $link);

				if ( $add_args )
					$link = esc_url( add_query_arg( $add_args, $link ) );

				$link .= $add_fragment;
				$page_links[] = "<li><a class='page-numbers' href='" . esc_url( apply_filters( 'paginate_links', $link ) ) . "'>$n_display</a></li>";
				$dots = true;
			} elseif ( $dots && ! $show_all ) {
				$page_links[] = '<li><span class="page-numbers dots">' . __( '&hellip;', 'knowledgepress' ) . '</span></li>';
				$dots = false;
			}
		}
	}

	if ( $prev_next && $current && ( $current < $total || -1 == $total ) ) {
		$link = str_replace('%_%', $format, $base);
		$link = str_replace('%#%', $current + 1, $link);

		if ( $add_args ) {
			$link = esc_url( add_query_arg( $add_args, $link ) );
		}

		$link .= $add_fragment;
		$page_links[] = '<li><a class="next page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $next_text . '</a></li>';
	}
	switch ( $type ) :
		case 'array' :
			return $page_links;
			break;
		case 'list' :
			$r .= '<ul class="page-numbers ' . $ss_framework->pagination_ul_class() . '">';
			$r .= join( '', $page_links );
			$r .= '</ul>';
			break;
		default :
			$r = join( '', $page_links);
			break;
	endswitch;
	return $r;
}

/**
 * Use pagination instead of pagers
 */
function shoestrap_pagination_toggler() {
	global $wp_query;

	if ( $wp_query->max_num_pages <= 1 ) {
		return;
	}

	$nav  = '<nav class="pagination">';
	$nav .= shoestrap_paginate_links(
		apply_filters( 'pagination_args', array(
			'base'      => str_replace( 999999999, '%#%', get_pagenum_link( 999999999 ) ),
			'format'    => '',
			'current'   => max( 1, get_query_var('paged') ),
			'total'     => $wp_query->max_num_pages,
			'prev_text' => '<i class="kp-arrow-left4"></i>',
			'next_text' => '<i class="kp-arrow-right4"></i>',
			'type'      => 'list',
			'end_size'  => 3,
			'mid_size'  => 3
		) )
	);
	$nav .= '</nav>';

	return $nav;
}
