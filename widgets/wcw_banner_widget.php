<?php

/**
 * @package wpcamp-widget-plugin
 */

class WCW_Banner_Widget extends WP_Widget {
  /**
   * Register widget with WordPress.
   */
  public function __construct() {
    parent::__construct(
      'wcw_banner_widget', // Base ID
      __('WordCamp Banner', 'wpcamp-widget-plugin'), // Name
      array( 'description' => __( 'Shows the WordCamp promotional banner of your liking.', 'wpcamp-widget-plugin' ), )
    );
  }

  /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
  public function widget( $args, $instance ) {
    // Do not even show the banner if it isn't set up properly.
    if (empty($instance['banner_obj'])) return;

    $banner_obj = $instance['banner_obj'];
    echo $args['before_widget'];

    require (dirname(__FILE__) . '/wcw_banner_widget/widget.php');

    echo $args['after_widget'];
  }

  /**
   * Back-end widget form.
   *
   * @see WP_Widget::form()
   *
   * @param array $instance Previously saved values from database.
   */
  public function form( $instance ) {
    if ( isset( $instance[ 'location' ] ) ) {
      $location = $instance[ 'location' ];
    } else {
      $location = '';
    }

    if ( isset( $instance[ 'year' ] ) ) {
      $year = $instance[ 'year' ];
    } else {
      $year = date_i18n('Y');
    }

    $wordcamp_data = false;
    if ( !empty($location) && false !== ($wordcamp_datas = get_option('wcw_wordcamp_datas', false)) ) {
      if ( isset($wordcamp_datas[$location . '_' . $year]) ) {
        $wordcamp_data = $wordcamp_datas[$location . '_' . $year];
      }
    }

    $banner = 0;
    if ( isset( $instance[ 'banner' ] ) ) {
      $banner = $instance[ 'banner' ];
    }

    require (dirname(__FILE__) . '/wcw_banner_widget/form.php');
  }

  /**
   * Sanitize widget form values as they are saved.
   *
   * @see WP_Widget::update()
   *
   * @param array $new_instance Values just sent to be saved.
   * @param array $old_instance Previously saved values from database.
   *
   * @return array Updated safe values to be saved.
   */
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['location'] = ( ! empty( $new_instance['location'] ) ) ? strip_tags( $new_instance['location'] ) : '';
    $instance['year'] = ( ! empty( $new_instance['year'] ) ) ? strip_tags( $new_instance['year'] ) : '';
    $instance['banner'] = ( ! empty( $new_instance['banner'] ) ) ? strip_tags( $new_instance['banner'] ) : '';

    // Update the wcw_banner_configs
    if (false === ($banner_configs = get_option('wcw_banner_configs', false))) {
      $banner_configs = array();
    }
    $banner_configs[$instance['location']] = array(
      'year' => $instance['year']
    );
    update_option('wcw_banner_configs', $banner_configs);

    // Do an update now, because we'll need these later...
    do_action('wcw_update_registered_wc_data_for_location_and_year', $instance['location'], $instance['year']);

    if (!empty($instance['banner'])) {
      $wordcamp_datas = get_option('wcw_wordcamp_datas', array());
      if ($wordcamp_data = @$wordcamp_datas[$instance['location'] . '_' . $instance['year']]) {
        foreach ($wordcamp_data->banners as $banner_obj) {
          if ($banner_obj->guid == $instance['banner']) {
            $banner_obj->href = $wordcamp_data->url;
            $instance['banner_obj'] = $banner_obj;
          }
        }
      }
    }

    return $instance;
  }
};
