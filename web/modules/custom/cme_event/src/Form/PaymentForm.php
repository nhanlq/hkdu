<?php

namespace Drupal\cme_event\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class PaymentForm.
 */
class PaymentForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'payment_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $productId = null, $uid = null) {
      $form['password'] = [
          '#type' => 'password',
          '#title' => 'Your Password/ New password',
          '#required' => true,
      ];
      $form['productId'] = [
          '#type' => 'hidden',
          '#value' => $productId,
      ];
      $form['uid'] = [
          '#type' => 'hidden',
          '#value' => $uid,
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


      $password = $form_state->getValue('password');
      $users = \Drupal::entityTypeManager()
          ->getStorage('user')
          ->loadByProperties([
              'uid' => $form_state->getValue('uid'),
          ]);

      $user = reset($users);
// Set the new password
      $user->setPassword($password);
// Save the user
      $user->save();

      //Auto login
      $username = $user->getUserName();
      if (\Drupal::service('user.auth')->authenticate($username, $password)) {
          user_login_finalize($user);
          $response = new RedirectResponse('/e-pharm/addcart/'.$form_state->getValue('productId'));
          $response->send();
      }
  }

}
