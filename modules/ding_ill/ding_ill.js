/**
 * Collapsible materials which are a part of an interlibrary order group.
 */
(function ($) {
  "use strict";

  Drupal.behaviors.ding_ill_collapsible = {
    attach: function(context, settings) {
      $('.js-ding-ill-collapsible', context).click(function(e) {
        $(this).toggleClass('ding-ill-collapsed');
        // Disable clicks/links if the material is collapsed. We only
        // want to expand here.
        if ($(this).hasClass('ding-ill-collapsed')) {
          e.preventDefault();
        }
      });
    }
  };


})(jQuery);
