(function() {
  var $, ajaxurl;

  $ = jQuery;

  ajaxurl = WCW_Admin.ajaxurl;

  $(document).on('change', 'select.wcw-location-select', function(e) {
    var $location_years, $this, location_slug, location_years;
    $this = $(this);
    location_slug = $this.val();
    location_years = window.wcw_location_array[location_slug];
    if (!location_slug || !location_years) {
      return false;
    }
    $location_years = $this.closest('form').find('select.wcw-location-year-select').empty();
    $("<option value='-1' selected='selected' disabled='disabled'>-- Select --</option>").appendTo($location_years);
    return $.each(location_years.years, function() {
      return $("<option></option>").appendTo($location_years).attr('value', this).text(this);
    });
  });

  $(document).on('change', 'select.wcw-location-year-select', function() {
    var $location, $this;
    $this = $(this);
    $location = $this.closest('form').find('select.wcw-location-select');
    if ($this.val() !== null && $location.val() !== null && parseInt($this.val(), 10) > 0) {
      return $this.closest('form').find('input[type="submit"]').click();
    }
  });

}).call(this);
