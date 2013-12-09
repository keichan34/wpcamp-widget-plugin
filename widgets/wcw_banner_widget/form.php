<p>
  <label for="<?php echo $this->get_field_id('location'); ?>"><?php _e('WordCamp Location Slug:', 'wpcamp-widget-plugin'); ?></label>
  <input class="widefat" type="text" id="<?php echo $this->get_field_id('location'); ?>" name="<?php echo $this->get_field_name('location'); ?>" value="<?php echo esc_attr($location); ?>" >
</p>
<p>
  <label for="<?php echo $this->get_field_id('year'); ?>"><?php _e('WordCamp Year:', 'wpcamp-widget-plugin'); ?></label>
  <input class="widefat" type="text" id="<?php echo $this->get_field_id('year'); ?>" name="<?php echo $this->get_field_name('year'); ?>" value="<?php echo esc_attr($year); ?>" >
</p>
<p>
  <?php if ( false === $wordcamp_data ): ?>
    <?php _e('The associated WordCamp data was not found or hasn\'t been synced yet. Please enter the "Location Slug" and "Year", then click "Save" to see the available banners.', 'wpcamp-widget-plugin'); ?>
  <?php else: ?>
    <?php if (count($wordcamp_data->banners) > 0): ?>
      <?php foreach($wordcamp_data->banners as $banner_obj): $id = $this->get_field_id('banner_' . $banner_obj->guid); ?>
        <label for="<?php echo $id; ?>">
          <input id="<?php echo $id; ?>" type="radio" name="<?php echo $this->get_field_name('banner'); ?>" value="<?php echo $banner_obj->guid; ?>" <?php if ($banner === $banner_obj->guid) echo 'checked="checked"'; ?>>
          <?php echo $banner_obj->title; ?> (<?php echo $banner_obj->width; ?>px × <?php echo $banner_obj->height; ?>px)
        </label>
      <?php endforeach; ?>
    <?php else: ?>
      <?php _e('The WordCamp was found, but no banners are available. Please try again later, or inform the organizers that you would like to post promotional banners on your blog!', 'wpcamp-widget-plugin'); ?>
    <?php endif; ?>
  <?php endif; ?>
</p>
