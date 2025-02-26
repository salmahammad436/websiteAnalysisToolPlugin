<?php
/**
 * Plugin Name: Website Analysis Tool
 * Description: Fetch and display website analysis data using a hashed URL.
 * Version: 1.3
 * Author: Salma
 */

if (!defined('ABSPATH')) {
    exit;
}

function website_analysis_enqueue_scripts() {
    wp_enqueue_script('website-analysis-script', plugin_dir_url(__FILE__) . 'website-analysis.js', array(), null, true);
    wp_enqueue_style('website-analysis-style', plugin_dir_url(__FILE__) . 'website-analysis.css', array(), null);
}
add_action('wp_enqueue_scripts', 'website_analysis_enqueue_scripts');
function website_analysis_shortcode() {
    ob_start();
    ?>
    <div id="website-analysis-result">
        <p>Loading analysis...</p>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('website_analysis', 'website_analysis_shortcode');

?>
