<?php
$locations = $this->wc_location_array();
$locations_hash = array();
foreach ($locations as $_location) {
  sort($_location->years);
  $locations_hash[$_location->title_for_slug] = array(
    'name' => $_location->name,
    'years' => $_location->years
  );
}
?>
<p>
  <label for="<?php echo $this->get_field_id('location'); ?>"><?php _e('WordCamp Location:', 'wpcamp-widget-plugin'); ?></label>
  <select class="widefat wcw-location-select" name="<?php echo $this->get_field_name('location'); ?>" id="<?php echo $this->get_field_id('location'); ?>">
    <?php
    foreach ($locations as $_location): ?>
      <option value="<?php echo esc_attr($_location->title_for_slug); ?>" <?php if ($location === $_location->title_for_slug) { echo 'selected="selected"'; } ?>><?php echo $_location->name; ?></option>
    <?php endforeach; ?>
  </select>
</p>
<p>
  <label for="<?php echo $this->get_field_id('year'); ?>"><?php _e('WordCamp Year:', 'wpcamp-widget-plugin'); ?></label>
  <select class="widefat wcw-location-year-select" name="<?php echo $this->get_field_name('year'); ?>" id="<?php echo $this->get_field_id('year'); ?>">
    <?php if (!empty($year) && !empty($location) && array_key_exists($location, $locations_hash)): ?>
      <?php foreach ($locations_hash[$location]['years'] as $_year): ?>
        <option value="<?php echo $_year; ?>" <?php if (intval($year) === intval($_year)) { echo 'selected="selected"'; } ?>><?php echo $_year; ?></option>
      <?php endforeach; ?>
    <?php endif; ?>
  </select>
</p>
<p>
  <?php if ( false === $wordcamp_data ): ?>
    <?php _e('The associated WordCamp data was not found or hasn\'t been synced yet. Please enter the "Location" and "Year", then click "Save" to see the available banners.', 'wpcamp-widget-plugin'); ?>
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
<script>
  window.wcw_location_array = <?php echo json_encode($locations_hash); ?>;
</script>
