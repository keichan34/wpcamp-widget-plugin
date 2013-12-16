(function() {
  var $, ajaxurl, init;

  $ = jQuery;

  ajaxurl = WCW_Admin.ajaxurl;

  init = function() {
    return $('select.wcw-location-select').each(function() {
      var $this;
      $this = $(this);
      if ($this.data('already-installed') === true) {
        return true;
      }
      $this.data('already-installed', true);
      return $this.change(function(e) {
        return $this.closest('form').find('input[type="submit"]').click();
      });
    });
  };

  $(document).on('ready', init);

  $(document).on('reload-wcw-location-selects', init);

}).call(this);
