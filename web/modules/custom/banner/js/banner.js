(function ($, Drupal) {

    'use strict';

    Drupal.behaviors.initSlick = {
        attach: function (context, settings) {
            $(".slick-slider").slick({
                dots: true,
                infinite: true,
                autoplay: true
            });

        }
    };

})(jQuery, Drupal);