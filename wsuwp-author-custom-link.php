<?php
/**
 * Plugin Name:     WSUWP Author Custom Link
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     Allows Author URLs to be sent to custom link.
 * Author:          Jake Weston
 * Author URI:      https://web.wsu.edu/wordpress/
 * Text Domain:     author-external-link
 * Version:         0.1.0
 *
 * @package         Author_Custom_Link
 */

namespace WSUWP\Author_Custom_Link;

// Define path and URL to the ACF plugin.
define( 'MY_ACF_PATH', plugin_dir_path( __FILE__ ) . 'includes/acf/' );
define( 'MY_ACF_URL', plugin_dir_url( __FILE__ ) . 'includes/acf/' );

// Include the ACF plugin.
require_once MY_ACF_PATH . 'acf.php';

class WSUWP_Author_External_Link {
	public function __construct() {
		add_filter( 'acf/settings/url', array( $this, 'my_acf_settings_url' ) );
		add_filter( 'acf/settings/show_admin', array( $this, 'my_acf_settings_show_admin' ) );
		add_filter( 'author_link', array( $this, 'generate_author_link' ), 10, 2 );

		// Save fields in functionality plugin
		add_filter( 'acf/settings/save_json', array( $this, 'get_local_json_path' ) );
		add_filter( 'acf/settings/load_json', array( $this, 'add_local_json_path' ) );
	}

	// Customize the url setting to fix incorrect asset URLs.
	public function my_acf_settings_url( $url ) {
		return MY_ACF_URL;
	}

	// (Optional) Hide the ACF admin menu item.
	public function my_acf_settings_show_admin( $show_admin ) {
		return false;
	}

	/**
	 * Define where the local JSON is saved
	 *
	 * @return string
	 */
	public function get_local_json_path() {
		return MY_ACF_PATH . 'acf-json';
	}

	/**
	 * Add our path for the local JSON
	 *
	 * @param array $paths
	 *
	 * @return array
	 */
	public function add_local_json_path( $paths ) {
		$paths[] = MY_ACF_PATH . 'acf-json';

		return $paths;
	}

	public function generate_author_link( $link, $id ) {
		$author = 'user_' . $id;
		if ( ! empty( get_field( 'external_author_url', $author ) ) ) {
			$location = get_field( 'external_author_url', $author );
			if ( is_author( $id ) ) {
				wp_redirect( $location, 301 );
				exit;
			}
			$link = get_field( 'external_author_url', $author );
		}
		return $link;
	}
}

new WSUWP_Author_External_Link();
