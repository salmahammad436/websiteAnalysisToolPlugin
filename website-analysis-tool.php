<?php
/*
Plugin Name: Website Analysis Plugin
Description: A plugin to send the site URL to an external API upon activation.
Version: 1.0
Author: Salma
*/

if (!defined('ABSPATH')) {
    exit; 
}
function website_analysis_activate() {
    error_log('🔹 Plugin activation function triggered.');

    $site_url = get_site_url();
    $hashed_url = hash('sha256', $site_url);
    $api_url = 'http://localhost:3001/api/websites/';

    $response = wp_remote_post($api_url, array(
        'method'    => 'POST',
        'body'      => json_encode(array('url' => $site_url, 'hashedUrl' => $hashed_url)),
        'headers'   => array('Content-Type' => 'application/json'),
        'data_format' => 'body'
    ));

    if (is_wp_error($response)) {
        error_log('❌ API Request Error: ' . $response->get_error_message());
        return;
    }
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!empty($data['id'])) {
        error_log('✅ Received ID from API: ' . $data['id']); 

        update_option('website_analysis_id', sanitize_text_field($data['id']));
        error_log('✅ Saved ID to database using update_option: ' . get_option('website_analysis_id'));

        set_transient('website_analysis_id', sanitize_text_field($data['id']), DAY_IN_SECONDS);
        error_log('✅ Saved ID using set_transient: ' . get_transient('website_analysis_id'));
    } else {
        error_log('❌ Failed to retrieve website ID from API response.');
    }
}
register_activation_hook(__FILE__, 'website_analysis_activate');


function website_analysis_get_site_id() {
    $site_id = get_option('website_analysis_id');
    
    if (!$site_id) {
        $site_id = get_transient('website_analysis_id');  
    }
    if ($site_id) {
        error_log('📤 Returning stored site ID: ' . $site_id);
        return rest_ensure_response(array('id' => $site_id));
    }
    return new WP_Error('no_id', 'No site ID found', array('status' => 404));
}
function website_analysis_register_rest_routes() {
    register_rest_route('website-analysis/v1', '/get-site-id', array(
        'methods'  => 'GET',
        'callback' => 'website_analysis_get_site_id',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'website_analysis_register_rest_routes');


function website_analysis_enqueue_scripts() {
    wp_enqueue_script('website-analysis', plugin_dir_url(__FILE__) . 'assets/js/website-analysis.js', array(), '1.0', true);
    wp_enqueue_style('website-analysis', plugin_dir_url(__FILE__) . 'assets/css/website-analysis.css', array(), null);
    wp_localize_script('website-analysis', 'websiteAnalysis', array(
        'restUrl' => rest_url('website-analysis/v1/get-site-id')
    ));
}
add_action('wp_enqueue_scripts', 'website_analysis_enqueue_scripts');

add_action('init', function() {
    error_log('🔍 Stored Site ID in DB: ' . get_option('website_analysis_id'));
    error_log('🔍 Stored Site ID in Transient: ' . get_transient('website_analysis_id'));
});

function website_analysis_tool() {
    ob_start();
    ?>
    <div id="website-analysis-result">Loading analysis...</div>
    <?php
    return ob_get_clean();
}
add_shortcode('website_analysis', 'website_analysis_tool');

?>
