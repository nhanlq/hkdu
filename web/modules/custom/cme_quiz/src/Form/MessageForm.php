<?php

namespace Drupal\cme_quiz\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class MessageForm.
 */
class MessageForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'message_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['message'] = [
            '#type' => 'text_format',
            '#title' => $this->t('Message'),
            '#weight' => '0',
            '#required' => true,
            '#default_value' => \Drupal::state()->get('quiz_message_done'),
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
        $data = $form_state->getValue('message');
        \Drupal::state()->set('quiz_message_done', $data['value']);
        \Drupal::messenger()->addMessage( 'Form save success.');
    }

}
