$ = jQuery



$(document).on 'change', 'select.wcw-location-select', (e) ->
  $this = $ this
  location_slug = $this.val()
  location_years = window.wcw_location_array[location_slug]
  return false if !location_slug or !location_years

  $location_years = $this.closest('form').find('select.wcw-location-year-select').empty()

  $("<option value='-1' selected='selected' disabled='disabled'>#{ WCW_Admin.select_location_year_placeholder }</option>").appendTo $location_years
  $.each location_years.years, () ->
    $("<option></option>").appendTo($location_years).attr('value', this).text this


$(document).on 'change', 'select.wcw-location-year-select', () ->
  $this = $ this
  $location = $this.closest('form').find('select.wcw-location-select')

  if $this.val() != null and $location.val() != null and parseInt($this.val(), 10) > 0
    # Load the available banners
    $this.closest('form').find('input[type="submit"]').click()
