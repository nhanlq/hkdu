<?php

namespace Drupal\cme_user\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Report.
 */
class Report extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'report';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,
    $uid=NULL) {
    $form['from'] = [
      '#type' => 'date',
      '#title' => $this->t('From'),
      '#weight' => '0',
    ];
    $form['to'] = [
      '#type' => 'date',
      '#title' => $this->t('To'),
      '#weight' => '0',
    ];

    $form['uid'] = [
      '#type' =>'hidden',
      '#value'=> $uid
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
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
//    foreach ($form_state->getValues() as $key => $value) {
//      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
//    }
    $uid = $form_state->getValue('uid');
    if($uid == "0"){
      $uid = \Drupal::currentUser()->id();
    }
    $host = \Drupal::request()->getSchemeAndHttpHost();
    $host = str_replace('https','http',$host);
    $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/wkhtmltopdf/generatepdf?url='.$host.'/report/'.$uid.'/'.$form_state->getValue('from').'/'.$form_state->getValue('to'))->toString());;
    $redirect->send();
  }

}
