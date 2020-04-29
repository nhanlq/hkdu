(function ($, Drupal) {

    'use strict';

    Drupal.behaviors.initColorboxGallery = {
        attach: function (context, settings) {
            if (!$.isFunction($.colorbox) || typeof settings.colorbox === 'undefined') {
                return;
            }
         //   if($(".gallery-group").length > 0){
                $(".gallery-group").colorbox();
         //   }

        }
    };

})(jQuery, Drupal);