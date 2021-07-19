<?php

namespace Drupal\epharm\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EmailNotify.
 */
class EmailNotify extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'email_notify';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['clinical_focus'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Email Notify Clinical Focus'),
            '#description' => $this->t('Each emnail is separated by sign ",".'),
            '#default_value' => \Drupal::state()->get('clinical_focus'),
            '#weight' => '0',
        ];
        $form['drug_news'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Email Notify Drug News'),
            '#description' => $this->t('Each emnail is separated by sign ",".'),
            '#default_value' => \Drupal::state()->get('drug_news'),
            '#weight' => '0',
        ];
        $form['pharm_dir'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Email Notify Pharm Dir'),
            '#description' => $this->t('Each emnail is separated by sign ",".'),
            '#default_value' => \Drupal::state()->get('pharm_dir'),
            '#weight' => '0',
        ];
        $form['special_offer'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Email Notify Special Offers'),
            '#description' => $this->t('Each emnail is separated by sign ",".'),
            '#default_value' => \Drupal::state()->get('special_offer'),
            '#weight' => '0',
        ];
        $form['event'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Email Notify Event'),
            '#description' => $this->t('Each emnail is separated by sign ",".'),
            '#default_value' => \Drupal::state()->get('event'),
            '#weight' => '0',
        ];
      $form['ads'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Email Notify Classified Ads'),
        '#description' => $this->t('Each emnail is separated by sign ",".'),
        '#default_value' => \Drupal::state()->get('ads'),
        '#weight' => '0',
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
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        foreach ($form_state->getValues() as $key => $value) {
            // @TODO: Validate fields.
        }
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // Display result.
        foreach ($form_state->getValues() as $key => $value) {

            \Drupal::state()->set($key, $value);
        }
        \Drupal::messenger()->addMessage('Update Email Notification success.');
    }

}
