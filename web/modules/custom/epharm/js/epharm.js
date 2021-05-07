(function ($, Drupal, drupalSettings) {

    Drupal.behaviors.initSwitchMenu = {
        attach: function (context, settings) {
            $(document).on('change','#epharm-switch',function(){
                if($(this).val()=='e-pharm'){
                    window.location.href = "/e-pharm";
                }else if($(this).val()=='cme'){
                    window.location.href = "/cme";
                }else if($(this).val()=='member-area') {
                  window.location.href = "/member-area";
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
