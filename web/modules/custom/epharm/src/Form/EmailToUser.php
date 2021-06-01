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
      if(empty($form_state->getValue('users')) && $form_state->getValue('all') != 1){
        $form_state->setErrorByName('users', t('Please enter special email or choose all.'));
      }
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'epharm';

    $key = 'toUser';
    $to = '';
    $to .= $form_state->getValue('users');
    if($form_state->getValue('all') == 1){
      $to .= $this->getAllUsers();
    }
    $to = \Drupal::state()->get('ads','leung0369@gmail.com');
    $params['title'] = $form_state->getValue('subject');
    $params['message'] = $form_state->getValue('message')['value'];
    $params['from'] = \Drupal::config('system.site')->get('mail');
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = TRUE;
    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

    if ($result['result'] !== TRUE) {
      \Drupal::messenger()->addMessage(t('There was a problem sending your message and it was not sent.'), 'error');
    }
    else {
      \Drupal::messenger()->addMessage(t('Your message has been sent.'));
    }
  }

  public function getAllUsers(){
    $ids = \Drupal::entityQuery('user')
    ->condition('status',1);
    $result = \Drupal\user\Entity\User::loadMultiple($ids);
    $email = [];
    if($result){
      foreach($result as $user){
        $email[] = $user->getEmail();
      }
      return implode(';',$email);
    }
    return false;
  }

}
