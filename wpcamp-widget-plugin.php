<?php
/**
 * Plugin Name: WordCamp Widget Plugin
 * Plugin URI: https://github.com/keichan34/wpcamp-widget-plugin
 * Description: Widgets and shortcodes to show promotional banners / information about WordCamps.
 * Version: 0.1
 * Author: Keitaroh Kobayashi
 * Author URI: http://keita.flagship.cc/
 * License: GPLv3
 * Text Domain: wpcamp-widget-plugin
 */
/**
  * @package wpcamp-widget-plugin
  */

if (!defined('WCW_VERSION')) {
  define('WCW_VERSION', '0.1');
}

require_once (dirname(__FILE__) . '/widgets/wcw_banner_widget.php');

class WordCampWidgetPlugin {

  function __construct() {
    add_action( 'widgets_init', array($this, 'widgets_init') );

    register_activation_hook(__FILE__, array($this, 'activated'));
    register_deactivation_hook(__FILE__, array($this, 'deactivated'));

    add_action( 'wcw_update_registered_wc_data', array($this, 'trigger_update') );
    add_action( 'wcw_update_registered_wc_data_for_location_and_year', array($this, 'update_data_for_location_and_year'), 10, 2 );

    # Register the admin AJAX endpoint
    // add_action( 'wp_ajax_wcw-get-locations', array($this, 'get_wc_location_array_ajax') );

    add_filter( 'wcw_api_url', array($this, 'api_url'), 10, 0 );

    add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_scripts') );
  }

  function api_url() {
    return 'https://wpcamp-api.herokuapp.com/v1';
  }

  function admin_enqueue_scripts() {
    // Register scripts and styles...
    wp_register_script( 'wpcamp-api-admin-script', $this->js_url('admin'), array('jquery'), WCW_VERSION );

    wp_localize_script( 'wpcamp-api-admin-script', 'WCW_Admin', array(
      'ajaxurl' => admin_url( 'admin-ajax.php' ),
      'data_fetch_error' => __( "We're sorry, but the WordCamp information was not available. Please try again.", 'wpcamp-widget-plugin' )
    ));

    wp_enqueue_script( 'wpcamp-api-admin-script' );
  }

  function widgets_init() {
    register_widget('WCW_Banner_Widget');
  }

  function activated() {
    wp_schedule_event( time(), 'daily', 'wcw_update_registered_wc_data' );
  }

  function deactivated() {
    wp_clear_scheduled_hook('wcw_update_registered_wc_data');
  }

  function trigger_update() {
    if (false === ($banner_configs = get_option('wcw_banner_configs', false))) {
      return false;
    }

    $default_attrs = array(
      'include_banners' => 'true'
    );

    $wordcamp_datas = array();

    foreach ($banner_configs as $location => $banner_config) {
      $attrs = array_merge($default_attrs, array(
        'year' => empty($banner_config['year']) ? date_i18n('Y') : $banner_config['year']
      ));

      $response = $this->data_for_location_and_attrs($location, $attrs);

      if (count($response->wordcamps) >= 1) {
        $wordcamp_data = $response->wordcamps[0];

        $wordcamp_datas[$location . '_' . $attrs['year']] = $wordcamp_data;
      }
    }

    update_option('wcw_wordcamp_datas', $wordcamp_datas);
    update_option('wcw_last_updated', time());
  }

  function update_data_for_location_and_year($location, $year) {
    $attrs = array(
      'include_banners' => 'true',
      'year' => $year
    );

    $response = $this->data_for_location_and_attrs($location, $attrs);

    $wordcamp_datas = get_option('wcw_wordcamp_datas', array());

    if (count($response->wordcamps) >= 1) {
      $wordcamp_data = $response->wordcamps[0];
      $wordcamp_datas[$location . '_' . $attrs['year']] = $wordcamp_data;
    }

    update_option('wcw_wordcamp_datas', $wordcamp_datas);
  }

  private function data_for_location_and_attrs($location, $attrs) {
    $url = $this->url_for_location(strtolower($location));
    return $this->response_for_url_and_attrs($url, $attrs);
  }

  private function url_for_location($location) {
    return $this->api_url() . "/wordcamps/location/{$location}.json";
  }

  private function response_for_url_and_attrs($url, $attrs = array()) {
    $url .= '?';
    $url .= implode('&', array_map(function($key, $value) {
      return $key . '=' . urlencode($value);
    }, array_keys($attrs), array_values($attrs)));

    $response = json_decode(file_get_contents($url));

    return $response;
  }

  private function js_url($file) {
    $debug = (defined('WP_DEBUG') && WP_DEBUG);
    $relative = $debug ? "js/src/{$file}.js" : "js/{$file}.min.js";
    return plugins_url( $relative, __FILE__ );
  }

  private function css_url($file) {
    $debug = (defined('WP_DEBUG') && WP_DEBUG);
    $relative = $debug ? "css/{$file}.css" : "js/{$file}.min.js";
    return plugins_url( $relative, __FILE__ );
  }

  function wc_location_array() {
    // 1 week == 604800 seconds
    // 1 day  == 86400  seconds
    if (time() - intval(get_option('wcw_cached_locations_date', time() - 604800)) >= 86400) {
      // The data is stale, so we'll refresh it.
      $url = $this->api_url() . '/wordcamps/list.json';
      $data = $this->response_for_url_and_attrs($url);

      // The meta value is empty for this API endpoint.
      $locations = $data->wordcamps;

      delete_option('wcw_cached_locations');
      add_option('wcw_cached_locations', $locations, null, 'no');

      update_option('wcw_cached_locations_date', time());
    } else {
      $locations = get_option('wcw_cached_locations');
    }

    return $locations;
  }
};

global $wcw_plugin;
$wcw_plugin = new WordCampWidgetPlugin();
