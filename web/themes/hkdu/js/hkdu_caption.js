(function ($, Drupal) {

    'use strict';

    Drupal.behaviors.initCaptionImg = {
        attach: function (context, settings) {
            $('img').each(function(){
                if($(this).attr('data-caption').length > -1){
                    $(this).next().html('<p class="caption-img">'+$(this).attr('data-caption')+'</p>');
                }
            });


        }
    };

})(jQuery, Drupal);