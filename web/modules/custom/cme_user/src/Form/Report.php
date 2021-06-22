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
  public function buildForm(
    array $form,
    FormStateInterface $form_state,
    $uid = NULL
  ) {
    $year = date('Y');
    $form['type'] = [
      '#type' => 'select',
      '#title' => t('Choose CME Type'),
      '#options' => ['period' => t('CME Type'),'date' =>t('CME Date')]
    ];
    $form['cycle_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Cycle Type'),
      '#options' => $this->cycle_type(),
      '#weight' => '0',
      '#states' => [
        // Only show this field when the 'toggle_me' checkbox is enabled.
        'visible' => [
          ':input[name="type"]' => [
            'value' => 'period',
          ],
        ],
      ],
    ];
    $form['from'] = [
      '#type' => 'date',
      '#title' => $this->t('Monthly from'),
      '#weight' => '0',
      '#states' => [
        // Only show this field when the 'toggle_me' checkbox is enabled.
        'visible' => [
          ':input[name="type"]' => [
            'value' => 'date',
          ],
        ],
      ],
    ];
    $form['to'] = [
      '#type' => 'date',
      '#title' => $this->t('Monthly to'),
      '#weight' => '0',
      '#states' => [
        // Only show this field when the 'toggle_me' checkbox is enabled.
        'visible' => [
          ':input[name="type"]' => [
            'value' => 'date',
          ],
        ],
      ],
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
    $uid = $form_state->getValue('uid');
    $user = \Drupal\user\Entity\User::load($uid);
    $type = $form_state->getValue('type') ;
    $period = $form_state->getValue('cycle_type');
    if($type == 'period'){
      $date = explode('-',$user->get('field_cme_join_date')->value);
      if($period == '2st'){
        if($date[0] > date('Y')){
          $form_state->setErrorByName('cycle_type', t('The Joining Date is in the future.'));
        }
        if((date('Y') - $date[0]) < 1){
          $form_state->setErrorByName('cycle_type', t('The 2nd Year is not available.'));
        }
      }
      if($period == '1st'){
        if($date[0] < date('Y') && (date('Y' ) - $date[0]) <= 1){
          $form_state->setErrorByName('cycle_type', t('The 1st Year is not available.'));
        }
      }
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
    $type = $form_state->getValue('type') ;
    if($type == 'period'){
      $start = $form_state->getValue('cycle_type');
    }else{
      $start = $form_state->getValue('from').'+'.$form_state->getValue('to');
    }
    if ($uid == "0") {
      $uid = \Drupal::currentUser()->id();
    }
    $host = \Drupal::request()->getSchemeAndHttpHost();
    $host = str_replace('https', 'http', $host);
    $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/wkhtmltopdf/generatepdf?url=' . $host . '/report/' . $uid . '/' . $type. '/' . $start . '/' . time())
      ->toString());;
    $redirect->send();
  }

  /**
   * @return array
   */
  public function cycle_type(){
    $data = [];
    $data['1st_01_01'] = '1st 01/01';
    $data['1st_07_01'] = '1st 07/01';
    $data['2nd_01_01'] = '2nd 01/01';
    $data['2nd_07_01'] = '2nd 07/01';
    $data['3rd_01_01'] = '3rd 01/01';
    $data['3rd_07_01'] = '3rd 07/01';
    return $data;
  }

}
