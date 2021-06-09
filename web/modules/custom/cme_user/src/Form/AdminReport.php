<?php

namespace Drupal\cme_user\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Report.
 */
class AdminReport extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'admin_report';
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
      '#options' => ['period' => t('CME Type')],
    ];
    $form['cycle_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Cycle Type'),
      '#options' => ['1st' => '1st year', '2nd' => '2nd Year', '3rd' => '3rd Year'],
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
    $type = $form_state->getValue('type');
    if ($type == 'period') {
      $start = $form_state->getValue('cycle_type');
    }
    $host = \Drupal::request()->getSchemeAndHttpHost();
    $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/admin/cme/report/' . $type . '/' . $start . '/')->toString());;
    $redirect->send();
  }

}
