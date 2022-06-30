<?php

if ( ! class_exists( 'Shoestrap_Background' ) ) {

	/**
	* The "Background" module
	*/
	class Shoestrap_Background {

		function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'css' ), 101 );
			add_action( 'plugins_loaded',     array( $this, 'upgrade_options' ) );
		}

		function css() {
			global $ss_settings;

			$bg_color        = $ss_settings['html_bg'];

			if ( isset( $bg_color ) ) {
				$bg_color = $bg_color;
			} else {
				$bg_color = '#ffffff';
			}

			// Style defaults to null.
			$style = null;

			wp_add_inline_style( 'knowledgepress_css', $style );
		}
	}
}
