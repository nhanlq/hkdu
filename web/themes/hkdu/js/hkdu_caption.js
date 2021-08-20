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


          $(".hkdu-icon-user").hover(function () {
            $('#user-tray').show();
          }, function () {
            $('#user-tray').hide();
          });
          $('#user-tray').hover(function () {
            $(this).show();
          }, function () {
            $(this).hide();
          });

          /**
           * notification
           */
          $(".notification-list a").each(function(){
            var id = $(this).attr('data-id');
            $(this).click(function(e){
              //  e.preventDefault(); // prevent form from reloading page
                $.ajax({
                  'url' : '/notify/'+id+'/update',
                  'type' : 'GET',
                });
              });
          });
          if($('img').length > -1){
            $('img').each(function(){
              if($(this).hasAttribute('data-caption')){
                if($(this).attr('data-caption').length > -1){
                  $(this).next().html('<p class="caption-img">'+$(this).attr('data-caption')+'</p>');
                }
              }

            });
          }

        }


    };

})(jQuery, Drupal);
