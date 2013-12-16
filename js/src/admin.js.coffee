$ = jQuery

ajaxurl = WCW_Admin.ajaxurl

init = () ->
  $('select.wcw-location-select').each () ->
    $this = $ this

    return true if $this.data('already-installed') == true
    $this.data 'already-installed', true

    $this.change (e) ->
      $this.closest('form').find('input[type="submit"]').click()


$(document).on 'ready', init
$(document).on 'reload-wcw-location-selects', init
