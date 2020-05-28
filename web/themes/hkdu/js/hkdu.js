(function ($, Drupal) {

    'use strict';

    Drupal.behaviors.initGlobalJS = {
        attach: function (context, settings) {
           $(".path-cart #edit-checkout").html('Proceed to Payment');
        }
    };

})(jQuery, Drupal);