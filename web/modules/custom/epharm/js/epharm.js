(function ($, Drupal, drupalSettings) {

    Drupal.behaviors.initSwitchMenu = {
        attach: function (context, settings) {
            $("#epharm-switch").on('change',function(){
                if($(this).val()=='e-pharm'){
                    window.location.href = "/e-pharm";
                }else if($(this).val()=='cme'){
                    window.location.href = "/cme";
                }else{
                    window.location.href = "/";
                }
            });
            // $("#epharm-switch").selectmenu();
            // $("#ui-id-1").click(function(){
            //     window.location.href = "/";
            // });
            // $("#ui-id-2").click(function(){
            //     window.location.href = "/e-pharm";
            // });
        }
    };

})(jQuery, Drupal, drupalSettings);