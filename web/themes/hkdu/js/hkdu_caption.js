(function ($, Drupal) {

    'use strict';

    Drupal.behaviors.initCaptionImg = {
        attach: function (context, settings) {
            $("article img").each(function(){
                if($(this).length > -1){
                    if($(this).attr('data-align') == 'center'){
                        $(this).addClass('image-center');
                    }else if($(this).attr('data-align') == 'right'){
                        $(this).addClass('image-right');
                    }
                }
            })

            $('img').each(function(){
                if($(this).attr('data-caption').length > -1){
                    $(this).next().html('<p class="caption-img">'+$(this).attr('data-caption')+'</p>');
                }
            });


        }
    };

})(jQuery, Drupal);