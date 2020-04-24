/**
 * Collapsible materials which are a part of an interlibrary order group.
 */
(function ($) {
  "use strict";

  Drupal.behaviors.ding_ill_collapsible = {
    attach: function(context, settings) {
      $('.js-ding-ill-material-group', context).each(function() {
        var group = $(this);
        var materials = group.find('.js-ding-ill-material');
        // Add a toggle element if the group has multiple materials.
        if (materials.length > 1) {
          var toggle = $(
            '<div class="js-ding-ill-material-group-toggle ding-ill--material-group-toggle">' +
              '<span class="ding-ill--material-group-toggle-text">' + Drupal.t('Show all editions') + '</span>' +
            '</div>'
          );
          toggle.click(function() {
            group.toggleClass('ding-ill--material-group__expanded');
          });

          // We need to append to the .inner element of the material because
          // of all the floating.
          materials.first().children()
            .append(toggle);
        }
      });
    }
  };


})(jQuery);
