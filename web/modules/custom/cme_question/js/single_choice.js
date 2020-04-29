(function ($, Drupal) {
    Drupal.behaviors.radio_options = {
        attach: function (context, settings) {

            $(document).ajaxComplete(function (event, xhr, settings) {
                    var selector1 = $('input[data-drupal-selector="edit-field-single-choice-0-subform-field-correct-answer-value"]');
                    selector1.each(function () {
                        $(this).on('click', function () {
                            //console.log($(this));
                            //   $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().find('input[type="radio"]').prop('checked', false);
                             selector2.prop('checked', false);
                            selector3.prop('checked', false);
                            selector4.prop('checked', false);
                            selector5.prop('checked', false);
                            selector6.prop('checked', false);
                            $(this).prop('checked', true);
                        });
                    });
                var selector2 = $('input[data-drupal-selector="edit-field-single-choice-1-subform-field-correct-answer-value"]');
                selector2.each(function () {
                    $(this).on('click', function () {
                        //console.log($(this));
                        //   $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().find('input[type="radio"]').prop('checked', false);
                        selector1.prop('checked', false);
                        selector3.prop('checked', false);
                        selector4.prop('checked', false);
                        selector5.prop('checked', false);
                        selector6.prop('checked', false);
                        $(this).prop('checked', true);
                    });
                });
                var selector3 = $('input[data-drupal-selector="edit-field-single-choice-2-subform-field-correct-answer-value"]');
                selector3.each(function () {
                    $(this).on('click', function () {
                        //console.log($(this));//   $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().find('input[type="radio"]').prop('checked', false);
                        selector2.prop('checked', false);
                        selector1.prop('checked', false);
                        selector4.prop('checked', false);
                        selector5.prop('checked', false);
                        selector6.prop('checked', false);
                        $(this).prop('checked', true);
                    });
                });
                var selector4 = $('input[data-drupal-selector="edit-field-single-choice-3-subform-field-correct-answer-value"]');
                selector4.each(function () {
                    $(this).on('click', function () {
                        //console.log($(this));
                        //   $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().find('input[type="radio"]').prop('checked', false);
                        selector2.prop('checked', false);
                        selector3.prop('checked', false);
                        selector1.prop('checked', false);
                        selector5.prop('checked', false);
                        selector6.prop('checked', false);
                        $(this).prop('checked', true);
                    });
                });
                var selector5 = $('input[data-drupal-selector="edit-field-single-choice-4-subform-field-correct-answer-value"]');
                selector5.each(function () {
                    $(this).on('click', function () {
                        //console.log($(this));
                        //   $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().find('input[type="radio"]').prop('checked', false);
                        selector2.prop('checked', false);
                        selector3.prop('checked', false);
                        selector4.prop('checked', false);
                        selector1.prop('checked', false);
                        selector6.prop('checked', false);
                        $(this).prop('checked', true);
                    });
                });
                var selector6 = $('input[data-drupal-selector="edit-field-single-choice-5-subform-field-correct-answer-value"]');
                selector6.each(function () {
                    $(this).on('click', function () {
                        //console.log($(this));
                        //   $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().find('input[type="radio"]').prop('checked', false);
                        selector2.prop('checked', false);
                        selector3.prop('checked', false);
                        selector4.prop('checked', false);
                        selector5.prop('checked', false);
                        selector1.prop('checked', false);
                        $(this).prop('checked', true);
                    });
                });
            });

            // $(document).ajaxComplete(function (event, xhr, settings) {
            //     if (settings.data != undefined) {
            //         // if (settings.data.indexOf("field_question") != -1) {
            //         var val = $(".field--name-field-question-type select").val();
            //         $(".field--name-field-question-type select").val('_none').change();
            //         $(".field--name-field-question-type select").val(val).change();
            //         $(".field--name-field-question-type select").trigger('change');
            //         //console.log(val);
            //         var valfront = $(".field--name-field-question-type-front select").val();
            //         $(".field--name-field-question-type-front select").val('_none');
            //         $(".field--name-field-question-type-front select").val(valfront).change();
            //         $(".field--name-field-question-type-front select").trigger('change');
            //         if (val == 'drag_drop') {
            //             $(".field--name-field-dropdown").hide();
            //         }
            //         //}
            //     }
            // });
        }
    }
})(jQuery, Drupal);