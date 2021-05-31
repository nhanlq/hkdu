<?php

namespace Drupal\epharm\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EmailNotify.
 */
class EmailToUser extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'email_touser';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subject'),
      '#required' => TRUE,
      '#weight' => '0',
    ];
    $form['all'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Send to all users in system'),
      '#weight' => '0',
    ];
    $form['users'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter special Email'),
      '#description' => $this->t('Every email, should user ";". EG: abc@domian,.com;def@domian.com'),
      '#weight' => '0',
    ];
    $form['message'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Message'),
      '#weight' => '0',
      '#required' => TRUE,
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
//
//      \Drupal::state()->set($key, $value);
//    }
    \Drupal::messenger()->addMessage('Update Email Notification success.');
  }

}
