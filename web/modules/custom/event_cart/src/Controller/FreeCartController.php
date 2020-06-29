<?php

namespace Drupal\event_cart\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class FreeCartController.
 */
class FreeCartController extends ControllerBase {

  /**
   * Free_cart.
   *
   * @return string
   *   Return Hello string.
   */
  public function free_cart($id, $type) {

      $user = \Drupal:: currentUser();
      $event = true;
      $cme_event = false;
      $url = '/e-pharm/event/'.$id;
      if($type=='cme'){
          $cme_event_entity = \Drupal\cme_event\Entity\CmeEvent::load($id);
          $score = \Drupal\cme_score\Entity\Score::create([
              'name' => 'Event Score of event ' . $cme_event_entity->getName() . ' of User ' . $user->getDisplayName(),
              'field_score' => 0,
              'field_user' => $user->id(),
              'field_event' => $cme_event_entity->id(),
              'field_attendance' => 0,
              'uid' => $user->id()
          ]);
          $score->save();
          //send QR code
          $body = '';
          $url = '';
          $host = \Drupal::request()->getSchemeAndHttpHost();
          $url = $host.'/cme/event/qrcode/'.$id.'/'.$user->id();
          $google_qr_image_url = "https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=" . $url . '&chld=H|0';

          // Write the alternate description for the QR image.
          $google_qr_alt = 'QR Code for '.$url;

          // Return markup, and return the block as being cached per URL path.
          $code =  array(
              '#theme' => 'image',
              '#uri' => $google_qr_image_url,
              '#width' => '250',
              '#height' => '250',
              '#alt' => $google_qr_alt,
              '#class' => 'google-qr-code-image',
              '#cache' => array(
                  'contexts' => array('url.path'),
              ),
          );
          $body .='<h2>Event information</h2>';
          $body .='<p><strong>Event:</strong> '.$cme_event_entity->getName().'</p>';
          $body .='<p><strong>Expired:</strong> '.$cme_event_entity->get('field_expired')->value.'</p>';
          $body .='<p><strong>Location:</strong> '.$cme_event_entity->get('field_location')->value.'</p>';
          $body .='<p><strong>Speaker:</strong> '.$cme_event_entity->get('field_speaker')->value.'</p>';
          $body .='<p><strong>Veune:</strong> '.$cme_event_entity->get('field_veune')->value.'</p>';
          $body .='<p><strong>QRCODE for Attendance:</strong></p>';
          $body .= render($code);
          $mailManager = \Drupal::service('plugin.manager.mail');
          $module = 'epharm';
          $key = 'sendQRcode';
          $to = $user->getEmail();
          $params['message'] = $body;
          $params['title'] = '[HKDU] QRCode for Event Attendance.';
          $params['user'] = $user->getDisplayName();
          $params['from'] = \Drupal::config('system.site')->get('mail');
          $attachment = array(
              'filepath' => '/sites/default/files/ics/ICS_event_'.$cme_event_entity->id().'.ics',
              'filename' => 'ICS_event_'.$cme_event_entity->id().'.ics',
              'filemime' => 'application/ics'
          );
          $params['attachments'][] = $attachment;
          $langcode = \Drupal::currentUser()->getPreferredLangcode();
          $send = true;

          $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
          if ($result['result'] !== true) {
              \Drupal::messenger()->addMessage(t('There was a problem sending your message and it was not sent.'), 'error');
          } else {
              \Drupal::messenger()->addMessage(t('Your message has been sent.'));
          }
          $event = false;
          $url = '/cme/event/'.$id;

      }

          $event_tracking = \Drupal\event_tracking\Entity\EventTracking::create([
              'name' => 'Order::'.$user->getAccountName().'::Free Event',
              'field_event' => $event ? $id : null,
              'field_cme_event' => $cme_event ? $id : null,
              'field_order' => null,
              'field_user' => $user->id(),
              'created' => time(),
              'uid' => $user->id(),
          ]);
          $event_tracking->save();

      $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput($url)->toString());
      $redirect->send();

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: free_cart with parameter(s): $id'),
    ];
  }

}
