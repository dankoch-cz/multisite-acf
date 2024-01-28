<?php
/*
Plugin Name: Multisite ACF
Description: Copy ACF values from one site to another in a multisite environment.
Version: 1.0
Author: Your Name
*/

if(!is_multisite()) {
	add_action('admin_notices', 'multisite_acf_missing_multisite_notice');
	function multisite_acf_missing_multisite_notice() {
		?>
		<div class="notice notice-error">
			<p><?php _e('Multisite ACF is intended for use in a WordPress Multisite environment.'); ?></p>
		</div>
		<?php
	}
}

function macf_check_acf() {
	if (!is_plugin_active('advanced-custom-fields/acf.php') && !is_plugin_active('advanced-custom-fields-pro/acf.php')) {
		// ACF is not installed or active
		add_action('admin_notices', 'multisite_acf_missing_acf_notice');
		function multisite_acf_missing_acf_notice() {
			?>
			<div class="notice notice-error">
				<p><?php _e('Multisite ACF requires the Advanced Custom Fields (ACF) plugin to be installed and activated.'); ?></p>
			</div>
			<?php
		}
	}
}
add_action( 'admin_init', 'macf_check_acf' );

define('MULTISITE_ACF_PLUGIN_URL', plugin_dir_url(__FILE__));


// Include main plugin class
require_once plugin_dir_path(__FILE__) . 'includes/api.php';

// Enqueue scripts and styles
function multisite_acf_enqueue_assets() {
	$version = filemtime(plugin_dir_path(__FILE__) . 'assets/multisite-acf.css');
	wp_enqueue_style('multisite-acf-style', plugin_dir_url(__FILE__) . 'assets/multisite-acf.css', array(), $version);

	$version = filemtime(plugin_dir_path(__FILE__) . 'assets/multisite-acf.js');
	wp_enqueue_script('multisite-acf-script', plugin_dir_url(__FILE__) . 'assets/multisite-acf.js', array('jquery'), $version, true);
}
add_action('admin_enqueue_scripts', 'multisite_acf_enqueue_assets');

// Add settings page under the Settings menu
function multisite_acf_settings_page() {
	add_options_page(
		'Multisite ACF Settings',
		'Multisite ACF',
		'manage_options',
		'multisite_acf_settings',
		'multisite_acf_render_settings_page'
	);
}
add_action('admin_menu', 'multisite_acf_settings_page');

// Render settings page
function multisite_acf_render_settings_page() {
	include_once plugin_dir_path(__FILE__) . 'includes/admin.php';
}
