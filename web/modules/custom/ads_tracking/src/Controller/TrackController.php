<?php

namespace Drupal\ads_tracking\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TrackController.
 */
class TrackController extends ControllerBase {

  /**
   * Track.
   *
   * @return string
   *   Return Hello string.
   */
  public function track($id) {
    $ads = \Drupal\advert\Entity\Ads::load($id);
    //update count
    $ads->set('field_total_count',($ads->get('field_total_count')->value + 1));
    $ads->save();
    $track = \Drupal\ads_tracking\Entity\Tracking::create([
      'name' => $ads->getName(),
      'created' =>time(),
      'field_ads' => ['target_id'=>$id],
      'field_count' => 1,
      'field_ip' => \Drupal::request()->getClientIp(),
    ]);
    $track->save();
    return new TrustedRedirectResponse($ads->get('field_link')->value, 302);
//    $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput($ads->get('field_link')->value)->toString());
//    $redirect->send();
//    return [];
  }


}
