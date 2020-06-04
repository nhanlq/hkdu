<?php

namespace Drupal\doctor\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
/**
 * Class ClinicController.
 */
class ClinicController extends ControllerBase {

  /**
   * Clinic.
   *
   * @return string
   *   Return Hello string.
   */
  public function clinic() {
      $doctor_id = $_GET['doctor_id'];
      $destination = $_GET['destination'];
      if(!isset($_GET['doctor_id'])){
          $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/admin/manage-doctor')->toString());
          $redirect->send();
      }
      if($store_id = $this->getLocation($doctor_id)){
          $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/store_locator/'.$store_id.'/edit?destination='.$destination.'&doctor_id='.$doctor_id)->toString());
          $redirect->send();
      }else{
          $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/store_locator/add?destination='.$destination.'&doctor_id='.$doctor_id)->toString());
          $redirect->send();
      }
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: clinic')
    ];
  }

  public function getLocation($id){
      $ids = \Drupal::entityQuery('store_locator')
          ->condition('field_doctor',$id)
          ->execute();
      $result = \Drupal\store_locator\Entity\StoreLocator::loadMultiple($ids);
      if($result){
          $store = reset($result);
          return $store->id();
      }else{
          return false;
      }

  }

}
