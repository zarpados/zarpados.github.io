<?php

/*
 * Get the content and widget areas for the footer
 */
function shoestrap_footer_content() {
	global $ss_framework;
	// Finding the number of active widget sidebars
	$num_of_sidebars = 0;

	for ( $i = 0; $i < 5; $i++ ) {
		$sidebar = 'sidebar-footer-' . $i;
		if ( is_active_sidebar( $sidebar ) ) {
			$num_of_sidebars++;
		}
	}

	// If sidebars exist, open row.
	if ( $num_of_sidebars >= 0 ) {
		echo $ss_framework->open_row( 'div' );
	}

	// Showing the active sidebars
	for ( $i = 0; $i < 5; $i++ ) {
		$sidebar = 'sidebar-footer-' . $i;

		if ( is_active_sidebar( $sidebar ) ) {
			// Setting each column width accordingly
			$col_class = 12 / $num_of_sidebars;
		
			echo $ss_framework->open_col( 'div', array( 'medium' => $col_class ) );
			dynamic_sidebar( $sidebar );
			echo $ss_framework->close_col( 'div' );

		}
	}

	// If sidebars exist, close row.
	if ( $num_of_sidebars >= 0 ) {
		echo $ss_framework->close_row( 'div' );

		// add a clearfix div.
		echo $ss_framework->clearfix();
	}

	do_action( 'shoestrap_footer_html' );
}

function is_footer_cta_active() {
	global $ss_settings;

	if ( !$ss_settings['footer_cta'] ) {
		return false;
	}

	$active = $ss_settings['footer_cta_pages'];

	if ( in_array('single', $active) && is_singular('post') ) {
		return true;
	} elseif ( in_array('category', $active) && is_category() ) {
		return true;
	} elseif ( in_array('page', $active) && is_page() ) {
		return true;
	} elseif ( in_array('knowledgebase', $active) && is_page_template('template-knowledgebase.php') ) {
		return true;
	} elseif ( in_array('vc', $active) && is_page_template('template-full.php') ) {
		return true;
	} else {
		return false;
	}

}