<?php

namespace Drupal\member_profile\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class ProfileController.
 */
class ProfileController extends ControllerBase {

  /**
   * Profile.
   *
   * @return string
   *   Return Hello string.
   */
  public function profile($uid) {
    $ids = \Drupal::entityQuery('doctor')
      ->condition('status', 1)
      ->condition('user_id', $uid)
      ->execute();
    $result = \Drupal\doctor\Entity\DoctorEntity::loadMultiple($ids);
    $doctor = reset($result);
    $element = [];
    if($doctor){
      $api_key = \Drupal::config('google_map_field.settings')
        ->get('google_map_field_apikey');
      $element = [
        '#theme' => 'google_map_field_embed',
        '#name' => $doctor->get('field_clinic_location')->name,
        '#lat' => $doctor->get('field_clinic_location')->lat,
        '#lon' => $doctor->get('field_clinic_location')->lon,
        '#zoom' => $doctor->get('field_clinic_location')->zoom,
        '#type' => $doctor->get('field_clinic_location')->type,
        '#show_marker' => $doctor->get('field_clinic_location')->marker === "1" ? "true" : "false",
        '#show_controls' => $doctor->get('field_clinic_location')->controls === "1" ? "true" : "false",
        '#width' => $doctor->get('field_clinic_location')->width ? $doctor->get('field_clinic_location')->width : '320px',
        '#height' => $doctor->get('field_clinic_location')->height ? $doctor->get('field_clinic_location')->height : '200px',
        '#api_key' => $api_key,
      ];
      if (!empty($doctor->get('field_clinic_location')->infowindow)) {
        $element['#infowindow'] = [
          '#markup' => $doctor->get('field_clinic_location')->infowindow,
          '#allowed_tags' => allowedTags(),
        ];
      }

      $element['#attached']['library'][] = 'google_map_field/google-map-field-renderer';
      $element['#attached']['library'][] = 'google_map_field/google-map-apis';
    }

    return [
      '#theme' => ['member_profile'],
      '#doctor' => $doctor,
      '#google_map'=>$element,
      '#uri' => \Drupal::service('path.current')->getPath(),
      '#uid'=> $uid,
      '#current_uid' =>\Drupal::currentUser()->id(),
      '#cache' => [
        'max-age' => 0,
      ],
    ];

  }

}
