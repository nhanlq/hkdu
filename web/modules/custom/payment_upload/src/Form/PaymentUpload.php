<?php

namespace Drupal\payment_upload\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class PaymentUpload.
 */
class PaymentUpload extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'payment_upload_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(
    array $form,
    FormStateInterface $form_state,
    $product_id = NULL
  ) {

    $form['receipt'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Upload Receipt'),
      '#description' => $this->t('Upload receipt. Accept pdf, pgn, jpg, jpeg, gif'),
      '#weight' => '0',
      '#upload_location' => 'public://payment/',
      '#multiple' => FALSE,
      '#required' => TRUE,
      // No need array, $config->get already give us an array format.
      '#upload_validators' => [
        'file_validate_extensions' => [
          'pdf png jpg jpeg gif',
        ],
      ],
    ];
    $form['product_id'] = [
      '#type' => 'hidden',
      '#value' => $product_id,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Upload to finish'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Display result.
    $user = \Drupal::currentUser();

    $product = \Drupal\commerce_product\Entity\Product::load($form_state->getValue('product_id'));
    if ($product->get('field_event')) {
      $event = \Drupal\event\Entity\Event::load($product->get('field_event')->target_id);
    }
    else {
      $event = \Drupal\cme_event\Entity\CmeEvent::load($product->get('field_cme_event')->target_id);
    }

    $file = $form_state->getValue('receipt');
    $payment = \Drupal\payment_upload\Entity\PaymentUpload::create([
      'name' => 'Payment Receipt upload of '.$user->getAccountName() . ' for event ' .
    $event->getName(),
      'field_user' => $user->id(),
      'field_product' => $form_state->getValue('product_id'),
      'field_receipt' => [
        'target_id' => $file[0],
        'alt' => 'Receipt',
        'title' => 'Receipt',
      ],
      'created' => time(),
      'uid' => $user->id(),
      'status' => 0,
    ]);

    $payment->save();

    $body = '';
    $body .='<h2>Event detail:</h2>';
    $body .='<p><strong>Event Name:</strong> '.$event->getName().'</p>';
    $body .='<p><a href="/admin/hkdu/payment_upload/'.$payment->id().'">Click to Approve for Receipt</a></p>';
    $body .= render($code);
    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'epharm';
    $key = 'payment';
    $to = 'nhanlq@outlook.com';'hkdu.content.notification@gmail.com';
    $params['message'] = $body;
    $params['title'] = '[HKDU] Payment Upload Receipt.';
    $params['user'] = $user->getDisplayName();
    $params['from'] = \Drupal::config('system.site')->get('mail');

    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;

    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
    if ($result['result'] !== true) {
      \Drupal::messenger()->addMessage(t('There was a problem sending your message and it was not sent.'), 'error');
    } else {
      \Drupal::messenger()->addMessage(t('Your message has been sent.'));
    }
    \Drupal::messenger()
      ->addMessage('Your Receipt has uploaded success. Please waiting for administrator approval.');
    $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/')->toString());;
    $redirect->send();
   // $form_state->setRedirectUrl('<front>');
  }

}
