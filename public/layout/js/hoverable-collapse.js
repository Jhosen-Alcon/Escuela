(function($) {
  'use strict';

  $(document).on('click', '.navbar-toggler', function() {
    var body = $('body');
    var sidebarIconOnly = body.hasClass("sidebar-icon-only");

    if (sidebarIconOnly) {
      body.removeClass('sidebar-icon-only');
    }
    else {
      body.addClass('sidebar-icon-only');
    }
  });

  $(document).on('click', '.navbar-toggle-mini', function() {
    var sidebar = $('#sidebar');

    if (sidebar.hasClass('active')) {
      sidebar.removeClass('active');
    }
    else {
      sidebar.addClass('active');
    }
  });

  $(document).on('mouseenter mouseleave', '.sidebar .nav-item', function(ev) {
    var body = $('body');
    var sidebarIconOnly = body.hasClass("sidebar-icon-only");
    var sidebarFixed = body.hasClass("sidebar-fixed");
    if (!('ontouchstart' in document.documentElement)) {
      if (sidebarIconOnly) {
        if (sidebarFixed) {
          if (ev.type === 'mouseenter') {
            body.removeClass('sidebar-icon-only');
          }
        } else {
          var $menuItem = $(this);
          if (ev.type === 'mouseenter') {
            $menuItem.addClass('hover-open')
          } else {
            $menuItem.removeClass('hover-open')
          }
        }
      }
    }
  });

})(jQuery);
