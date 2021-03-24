(function ($, Drupal) {

    'use strict';

    Drupal.behaviors.doctorAdmin = {
        attach: function (context, settings) {
            /**set default for normal date*/
            $('#edit-field-normal-days-0-inline-entity-form-field-normal-day-0-value').val(Drupal.t('MONDAY 星期一'));
            $('#edit-field-normal-days-1-inline-entity-form-field-normal-day-0-value').val(Drupal.t('TUESDAY 星期二'));
            $('#edit-field-normal-days-2-inline-entity-form-field-normal-day-0-value').val(Drupal.t('WEDNESDAY 星期三'));
            $('#edit-field-normal-days-3-inline-entity-form-field-normal-day-0-value').val(Drupal.t('THURSDAY 星期四'));
            $('#edit-field-normal-days-4-inline-entity-form-field-normal-day-0-value').val(Drupal.t('FRIDAY 星期五'));
            $('#edit-field-normal-days-5-inline-entity-form-field-normal-day-0-value').val(Drupal.t('SATURDAY 星期六'));
            $('#edit-field-normal-days-6-inline-entity-form-field-normal-day-0-value').val(Drupal.t('SUNDAY 星期日'));
            /** Set Defasult for CNY */
           // console.log($('#edit-field-cny-date-0-inline-entity-form-field-cny-date option:eq(1)'));
            $('#edit-field-cny-date-0-inline-entity-form-field-cny-date option:eq(1)').attr('selected', 'selected');
            $('#edit-field-cny-date-1-inline-entity-form-field-cny-date option:eq(2)').prop('selected', true);
            $('#edit-field-cny-date-2-inline-entity-form-field-cny-date option:eq(3)').prop('selected', true);
            $('#edit-field-cny-date-3-inline-entity-form-field-cny-date option:eq(4)').prop('selected', true);
            $('#edit-field-cny-date-4-inline-entity-form-field-cny-date option:eq(5)').prop('selected', true);
            $('#edit-field-cny-date-5-inline-entity-form-field-cny-date option:eq(6)').prop('selected', true);
            $('#edit-field-cny-date-6-inline-entity-form-field-cny-date option:eq(7)').prop('selected', true);
            $('#edit-field-cny-date-7-inline-entity-form-field-cny-date option:eq(8)').prop('selected', true);

        }
    };

})(jQuery, Drupal);