<?php

/**
 * Plugin Name: Ultimate Addon for Beaver Builder (Lite)
 * Plugin URI: http://www.ultimatebeaver.com/
 * Description: Ultimate Addons is a premium extension for Beaver Builder that adds 30+ modules, 100+ templates and works on top of any Beaver Builder Package. (Free, Standard, Pro & Agency) You can use it with on any WordPress theme.
 * Version: 1.0.0
 * Author: Brainstorm Force
 * Author URI: http://www.brainstormforce.com
 * Text Domain: uabb
 */


if( !class_exists( "BB_Ultimate_Addon" ) ) {

	define( 'BB_ULTIMATE_ADDON_DIR', plugin_dir_path( __FILE__ ) );
	define( 'BB_ULTIMATE_ADDON_URL', plugins_url( '/', __FILE__ ) );
	define( 'BB_ULTIMATE_ADDON_LITE_VERSION', '1.0.0' );
	define( 'BSF_REMOVE_uabb_FROM_REGISTRATION_LISTING', true );

	class BB_Ultimate_Addon {

		/*
		* Constructor function that initializes required actions and hooks
		* @Since 1.0
		*/
		function __construct() {

			register_activation_hook( __FILE__, array( $this, 'activation_reset' ) );

			//	UABB Initialize
			require_once 'classes/class-uabb-init.php';
		}

		function activation_reset() {

			$no_memory = $this->check_memory_limit();

				if( $no_memory == true && ! defined( 'WP_CLI' ) ) {

					$msg  = sprintf( __('Unfortunately, plugin could not be activated as the memory allocated by your host has almost exhausted. UABB plugin recommends that your site should have 15M PHP memory remaining. <br/><br/>Please check <a target="_blank" href="https://www.ultimatebeaver.com/docs/increase-memory-limit-site/">this</a> article for solution or contact <a target="_blank" href="http://store.brainstormforce.com/support">support</a>.<br/><br/><a class="button button-primary" href="%s">Return to Plugins Page</a>', 'uabb'), network_admin_url( 'plugins.php' ) );

					deactivate_plugins( plugin_basename( __FILE__ ) );
					wp_die( $msg );
				}

			delete_option( 'uabb_hide_branding' );

			// Force check graupi bundled products
			update_site_option( 'bsf_force_check_extensions', true );
		}

		function check_memory_limit() {

			$memory_limit  = ini_get('memory_limit'); 		//	Total Memory
			$peak_memory   = memory_get_peak_usage(true);	//	Available Memory
			$uabb_required = 14999999;						//	Required Memory for UABB

			if( preg_match('/^(\d+)(.)$/', $memory_limit, $matches ) ) {

			    switch( $matches[2] ) {
			    	case 'K': 	$memory_limit = $matches[1] * 1024; 				break;
			    	case 'M': 	$memory_limit = $matches[1] * 1024 * 1024; 			break;
			    	case 'G': 	$memory_limit = $matches[1] * 1024 * 1024 * 1024; 	break;
			    }
			}

			if( $memory_limit - $peak_memory <= $uabb_required ) {
				return true;
			} else {
				return false;
			}
		}
	}

	new BB_Ultimate_Addon();
} else {
	// Display admin notice for activating beaver builder
	add_action( 'admin_notices', 'admin_notices' );
	add_action( 'network_admin_notices', 'admin_notices' );

	function admin_notices() {

		$url = admin_url( 'plugins.php' );

		echo '<div class="notice notice-error"><p>';
		echo sprintf( __( "You currently have two versions of <strong>Ultimate Addon for Beaver Builder</strong> active on this site. Please <a href='%s'>deactivate one</a> before continuing.", 'uabb' ), $url );
	    echo '</p></div>';

  	}
}