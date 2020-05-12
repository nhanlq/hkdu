<?php

namespace Drupal\cme_event\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EventRegisterOffline.
 */
class EventRegisterOffline extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'event_register_offline';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['event'] = [
            '#type' => 'select',
            '#title' => 'CME Event',
            '#options' => $this->getEvents(),
            '#required' => true,
        ];
        $form['fname'] = [
            '#title' => 'First Name',
            '#type' => 'textfield',
            '#required' => true,
        ];
        $form['lname'] = [
            '#title' => 'Last Name',
            '#type' => 'textfield',
            '#required' => true,
        ];
        $form['email'] = [
            '#title' => 'Email',
            '#type' => 'email',
            '#required' => true,
        ];
        $form['tel'] = [
            '#title' => 'Telephone',
            '#type' => 'textfield',
            '#required' => true,
        ];
        $form['mchk_number'] = [
            '#title' => 'MCKH Number',
            '#type' => 'textfield',
            '#required' => true,
        ];
        $form['specialty'] = [
            '#title' => 'Specialty',
            '#type' => 'textfield',
            '#required' => true,
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
        // check user exits
        if (!empty($form_state->getValue('mchk_number')) && $user = $this->getUserByReno($form_state->getValue('mchk_number'))) {
            $uid = $user->id();

        } elseif (!empty($form_state->getValue('email'))) {
            if ($user = user_load_by_mail($form_state->getValue('email'))) {
                $uid = $user->id();
            }
        }else{
            $user = \Drupal\user\Entity\User::create();
            //Mandatory settings
            $user->setPassword('@123456');
            $user->enforceIsNew();
            $user->setEmail($form_state->getValue('email'));
            $user->setUsername($form_state->getValue('fname').$form_state->getValue('lname'));
            $user->set('field_first_name',$form_state->getValue('fname'));
            $user->set('field_last_name',$form_state->getValue('lname'));
            $user->set('field_contact_number',$form_state->getValue('tel'));
            $user->set('field_registration_no',$form_state->getValue('mchk_number'));
            $user->set('field_specialty',$form_state->getValue('specialty'));
            //Optional settings
            $user->activate();
            //Save user
             $user->save();
             $uid = $user->id();
        }
        $event_id = $form_state->getValue('event');
        $event = \Drupal\cme_event\Entity\CmeEvent::load($event_id);
        $body = '';
        $product = getCmeProduct($event_id);
        $host = \Drupal::request()->getSchemeAndHttpHost();
        $body .='<p>Thank you for register event '.$event->getName().'</p>';
        $link = $host.'/cme/event/'.$event_id.'/'.$uid.'/'.$product->get('product_id')->value.'/payment';
        \Drupal::messenger()->addMessage(t('Payment Link: '.$link));
            $body .='<p>To final registertration this event, please click this link <a href="'.$link.'">'.$link.'</a> to finish payment step.</p>';
        $mailManager = \Drupal::service('plugin.manager.mail');
        $module = 'epharm';
        $key = 'sendEventPayment';
        $to = $form_state->getValue('email');
        $params['message'] = '<a href=""></a>';
        $params['title'] = '[HKDU] Link for payment settlement for the event.';
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

    }

    public function getEvents()
    {
        $ids = \Drupal::entityQuery('cme_event')
            ->condition('status', 1)
            ->sort('field_weight', 'ASC')
            ->sort('created', 'DESC')
            ->execute();
        $events = \Drupal\cme_event\Entity\CmeEvent::loadMultiple($ids);
        $data = [];
        if ($events) {
            foreach ($events as $event) {
                $data[$event->id()] = $event->getName();
            }
        }
        return $data;
    }

    public function getUserByReno($id)
    {
        $ids = \Drupal::entityQuery('user')
            ->condition('status', 1)
            ->condition('field_registration_no', $id)
            ->execute();
        $results = \Drupal\user\Entity\User::loadMultiple($ids);
        if ($results) {
            return reset($results);
        } else {
            return false;
        }
    }

}
