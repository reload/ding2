/**
 * Collapsible materials which are a part of an interlibrary order group.
 */
(function ($) {
  "use strict";

  Drupal.behaviors.ding_ill_collapsible = {
    attach: function(context, settings) {
      $('.js-ding-ill-collapsible', context).click(function(e) {
        $(this).toggleClass('ding-ill-collapsed');
        e.preventDefault();
      });
    }
  };


})(jQuery);
