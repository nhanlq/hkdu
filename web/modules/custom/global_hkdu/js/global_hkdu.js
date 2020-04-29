(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.initSlickSlider = {
        attach: function (context, settings) {
            $(".slick-slider").slick({
                dots: true,
                infinite: true,
                autoplay: true
            });

        }
    };

})(jQuery, Drupal, drupalSettings);