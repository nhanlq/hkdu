<?php

namespace Drupal\payment_upload\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class PaymentController.
 */
class PaymentController extends ControllerBase {

  /**
   * Payment.
   *
   * @return string
   *   Return Hello string.
   */
  public function payment($event_id, $product_id, $type) {
    if ($type == 'cme') {
      $event = \Drupal\cme_event\Entity\CmeEvent::load($event_id);
    }
    else {
      $event = \Drupal\event\Entity\Event::load($event_id);
    }
    return [
      'results' => [
        '#theme' => 'payment_method',
        '#event' => $event,
        '#product_id' => $product_id,
      ],
      'pager' => [
        '#type' => 'pager',
      ],
    ];
  }

  /**
   * Action.
   *
   * @return string
   *   Return Hello string.
   */
  public function action() {
    $product_id = $_POST['product_id'];
    if (isset($_POST['type']) && $_POST['type'] == 'upload') {
      $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/payment/upload/receipt/' . $product_id)
        ->toString());;
      $redirect->send();
    }
    else {
      $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/e-pharm/addcart/' . $product_id)
        ->toString());;
      $redirect->send();
    }

    return [];
  }

  /**
   * Approval.
   *
   * @return string
   *   Return Hello string.
   */
  public function approval($id) {
    $payment = \Drupal\payment_upload\Entity\PaymentUpload::load($id);
    $user = \Drupal\user\Entity\User::load($payment->get('field_user')->target_id);
    $product = \Drupal\commerce_product\Entity\Product::load($payment->get('field_product')->target_id);
    $event = FALSE;
    $cme_event = FALSE;
    $body = '';
    $host = \Drupal::request()->getSchemeAndHttpHost();
    if ($product->get('field_event')->target_id > 0) {
      $event = $product->get('field_event')->target_id;
      $event_entity = \Drupal\event\Entity\Event::load($event);
      $body .= '<h2>Event information</h2>';
      $body .= '<p><strong>Event:</strong> ' . $event_entity->getName() . '</p>';
      $body .= '<p><strong>Expired:</strong> ' . date('d/m/Y',
          $event_entity->get('field_expired')->value) . '</p>';
      $body .= '<p><strong>Location:</strong> ' . $event_entity->get('field_location')->value . '</p>';
      $body .= render($code);
      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'epharm';
      $key = 'sendQRcode';
      $to = $user->getEmail();
      $params['message'] = $body;
      $params['title'] = '[HKDU] Event Calendar.';
      $params['user'] = $user->getDisplayName();
      $params['from'] = \Drupal::config('system.site')->get('mail');
      $attachment = [
        'filepath' => $host . '/sites/default/files/ics/ICS_event_' . $event_entity->id() . '.ics',
        'filename' => 'ICS_event_' . $event_entity->id() . '.ics',
        'filemime' => 'application/ics',
      ];
      $params['attachments'][] = $attachment;
      $langcode = \Drupal::currentUser()->getPreferredLangcode();
      $send = TRUE;

      $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL,
        $send);
      if ($result['result'] !== TRUE) {
        \Drupal::messenger()
          ->addMessage(t('There was a problem sending your message and it was not sent.'),
            'error');
      }
      else {
        \Drupal::messenger()->addMessage(t('Your message has been sent.'));
      }
    }
    if ($product->get('field_cme_event')->target_id > 0) {
      $cme_event = $product->get('field_cme_event')->target_id;
      $cme_event_entity = \Drupal\cme_event\Entity\CmeEvent::load($cme_event);
      $score = \Drupal\cme_score\Entity\Score::create([
        'name' => 'Event Score of event ' . $cme_event_entity->getName() . ' of User ' . $user->getDisplayName(),
        'field_score' => 0,
        'field_user' => $user->id(),
        'field_event' => $cme_event_entity->id(),
        'field_attendance' => 0,
        'uid' => $user->id(),
      ]);
      $score->save();
      //send QR code

      $url = '';
      $host = \Drupal::request()->getSchemeAndHttpHost();
      $url = $host . '/cme/event/qrcode/' . $cme_event . '/' . $user->id();
      $google_qr_image_url = "https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=" . $url . '&chld=H|0';

      // Write the alternate description for the QR image.
      $google_qr_alt = 'QR Code for ' . $url;

      // Return markup, and return the block as being cached per URL path.
      $code = [
        '#theme' => 'image',
        '#uri' => $google_qr_image_url,
        '#width' => '250',
        '#height' => '250',
        '#alt' => $google_qr_alt,
        '#class' => 'google-qr-code-image',
        '#cache' => [
          'contexts' => ['url.path'],
        ],
      ];
      $body .= '<h2>Event information</h2>';
      $body .= '<p><strong>Event:</strong> ' . $cme_event_entity->getName() . '</p>';
      $body .= '<p><strong>Expired:</strong> ' . $cme_event_entity->get('field_expired')->value . '</p>';
      $body .= '<p><strong>Location:</strong> ' . $cme_event_entity->get('field_location')->value . '</p>';
      $body .= '<p><strong>Speaker:</strong> ' . $cme_event_entity->get('field_speaker')->value . '</p>';
      $body .= '<p><strong>Veune:</strong> ' . $cme_event_entity->get('field_veune')->value . '</p>';
      $body .= '<p><strong>QRCODE for Attendance:</strong></p>';
      $body .= render($code);
      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'epharm';
      $key = 'sendQRcode';
      $to = $user->getEmail();
      $params['message'] = $body;
      $params['title'] = '[HKDU] QRCode for Event Attendance.';
      $params['user'] = $user->getDisplayName();
      $params['from'] = \Drupal::config('system.site')->get('mail');
      $attachment = [
        'filepath' => $host . '/sites/default/files/ics/ICS_CME_event_' . $cme_event_entity->id() . '.ics',
        'filename' => 'ICS_event_' . $cme_event_entity->id() . '.ics',
        'filemime' => 'application/ics',
      ];
      $params['attachments'][] = $attachment;
      $langcode = \Drupal::currentUser()->getPreferredLangcode();
      $send = TRUE;

      $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL,
        $send);
      if ($result['result'] !== TRUE) {
        \Drupal::messenger()
          ->addMessage(t('There was a problem sending your message and it was not sent.'),
            'error');
      }
      else {
        \Drupal::messenger()->addMessage(t('Your message has been sent.'));
      }
    }
    $event_tracking = \Drupal\event_tracking\Entity\EventTracking::create([
      'name' => 'Order::' . $user->getAccountName(),
      'field_event' => isset($event) ? $event : NULL,
      'field_cme_event' => isset($cme_event) ? $cme_event : NULL,
      // 'field_order' => $order->getOrderNumber(),
      'field_user' => $user->id(),
      'created' => time(),
      'uid' => $user->id(),
    ]);
    $event_tracking->save();

    $payment->set('status', 1);
    $payment->save();

    \Drupal::messenger()->addMessage(t('Approval success.'));
    $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/admin/hkdu/payment_upload/' . $payment->id())
      ->toString());;
    $redirect->send();
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: approval with parameter(s): $id'),
    ];
  }

}
