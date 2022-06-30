<?php

if ( ! class_exists( 'SS_Framework' ) ) {

	/**
	* The "Advanced" module
	*/
	class SS_Framework {

		/**
		 * Class constructor
		 */
		function __construct() {
			global $ss_settings;

			require_once dirname( __FILE__ ) . '/core/class-SS_Framework_Core.php';

			do_action( 'shoestrap_include_frameworks' );

			if ( ! defined( 'SS_FRAMEWORK' ) ) {
				$active_framework = 'bootstrap';
			}

			// If the active framework is Bootstrap, include it.
			if ( ( defined( 'SS_FRAMEWORK' ) && 'bootstrap' == SS_FRAMEWORK ) || ! defined( 'SS_FRAMEWORK' ) ) {
				require_once 'bootstrap/framework.php';
			}

			global $ss_active_framework;

			$compiler = false;
			// Return the classname of the active framework.
			$active   = $ss_active_framework['classname'];

			$compiler = $ss_active_framework['compiler'];

			global $ss_framework;
			$ss_framework = new $active;

		}
	}
}

$framework = new SS_Framework();
