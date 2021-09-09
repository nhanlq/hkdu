(function ($, Drupal) {

    'use strict';

    Drupal.behaviors.initGlobalJS = {
        attach: function (context, settings) {
           $(".path-cart #edit-checkout").html('Proceed to Payment');

           //advance search
           $("a.advance-search").click(function(e){
              e.preventDefault();
               $(".advance_search_detail").show();

           });
           if($(".waiting-thank").length > 0){
             $(".waiting-thank").click(function(e){
               e.preventDefault();
                $(".waiting-confirm").show();
             });
           }
        }
    };


})(jQuery, Drupal);
