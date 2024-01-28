<?php

// api.php

// Register the API endpoint for POST requests
add_action('rest_api_init', function () {

	register_rest_route('multisite-acf/v1', '/page-data/', array(
		'methods' => 'POST',
		'callback' => 'macf_get_page_data',
	));

	register_rest_route('multisite-acf/v1', '/acf-data/', array(
		'methods' => 'POST',
		'callback' => 'macf_get_acf_data',
	));

	register_rest_route('multisite-acf/v1', '/submit-form/', array(
		'methods' => 'POST',
		'callback' => 'macf_submit_form',
	));
});

function macf_get_page_data() {
	// Get the site ID from the POST request
	$site_id = isset($_POST['site_id']) ? intval($_POST['site_id']) : 1; // Default to site ID 1

	// Use the site ID to retrieve posts
	$args = array(
		'post_type' => array('page', 'post'),  // Adjust the post type as needed
		'posts_per_page' => -1,
		'post_status'    => array('publish', 'draft'),
	);

	// Add site-specific parameter to the query
	switch_to_blog($site_id);

	$query = new WP_Query($args);
	$posts = $query->posts;

	// Restore the original site
	restore_current_blog();

	// Prepare data based on posts
	$data = array();

	$data[] = array(
		'value' => '',
		'label' => 'Select page'
	);

	foreach ($posts as $post) {
		$value = $post->ID;
		$label = $post->post_title;

		if(get_post_status($post) == 'draft') {
			$label .= ' (Draft)';
		}

		$data[] = array(
			'value' => $value,
			'label' => $label,
		);
	}

	// Return the data as JSON
	wp_send_json($data);
}

function macf_get_acf_data() {
	// Get the site ID from the POST request
	$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 1; // Default to site ID 1

	// Initialize an array to store the ACF field groups and their custom fields
	$acf_data = array();

	// Get all ACF field groups
	$field_groups = acf_get_field_groups();

	// Loop through each field group
	foreach ($field_groups as $field_group) {
		// Get the custom fields for the current field group
		$fields = acf_get_fields($field_group['key']);

		// Initialize an array to store custom field data
		$field_data = array();

		// Loop through each custom field
		foreach ($fields as $field) {
			// Get the value of the custom field for the specific post
			$field_value = get_field($field['key'], $post_id);

			// Add custom field data to the array
			$field_data[] = array(
				'label' => $field['label'], // Add the field label
				'name' => $field['name'],
				'key' => $field['key'],
				'type' => $field['type'],
				'value' => $field_value, // Add the field value
				// Add more field properties as needed
			);
		}

		// Add field group data to the array
		$acf_data[] = array(
			'group' => array(
				'key' => $field_group['key'],
				'title' => $field_group['title'],
				// Add more group properties as needed
			),
			'fields' => $field_data,
		);
	}

	// Return the data as JSON
	wp_send_json($acf_data);
}

function macf_submit_form() {

	$transferredData = array();
	$destinationPage = sanitize_text_field($_POST['destination_page']);
	$destinationSite = sanitize_text_field($_POST['destination_site']);
	$sourceCustomFields = isset($_POST['source_custom_fields']) ? $_POST['source_custom_fields'] : array();
	$sourcePage = sanitize_text_field($_POST['source_page']);
	$sourceSite = sanitize_text_field($_POST['source_site']);

	switch_to_blog($sourceSite);

	foreach ($sourceCustomFields as $acf) {
		$transferredData[] = array(
			'key' => $acf,
			'value' => get_field($acf, $sourcePage)
		);
	}

	switch_to_blog($destinationSite);

	foreach($transferredData as $acf) {
		update_field($acf['key'], $acf['value'], $destinationPage);
	}

	restore_current_blog();

	wp_send_json('success');
}