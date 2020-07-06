<?php

namespace Drupal\event\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\event_tracking\Entity\EventTracking;

/**
 * Class EnrollForm.
 */
class EnrollForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'enroll_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['email'] = [
            '#type' => 'email',
            '#title' => $this->t('Email'),
            '#weight' => '0',
            '#required' =>true
        ];
        $form['fname'] = [
            '#type' => 'textfield',
            '#title' => $this->t('First Name'),
            '#weight' => '1',
            '#required' =>true
        ];
        $form['lname'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Last Name'),
            '#weight' => '2',
            '#required' =>true
        ];
        $form['event'] = [
            '#type' => 'select',
            '#title' => $this->t('Event'),
            '#description' => $this->t('Choose event'),
            '#options' => $this->getEvents(),
            '#weight' => '3',
            '#required' =>true
        ];
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#weight' => '4',
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
            if($key=='email' && empty($value)){
                $form_state->setErrorByName('email', t('Email is required.'));
            }
        }
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // Display result.
        $product = $this->getEventProduct($form_state->getValue('event'));
        //load product variation
        $entity_manager = \Drupal::entityManager();
        $product_variation = $entity_manager->getStorage('commerce_product_variation')->load((int)$product->getVariationIds()[0]);
        //create order item

        $order_item = \Drupal\commerce_order\Entity\OrderItem::create([
            'type' => 'default',
            'purchased_entity' => $product_variation,
            'quantity' => 1,
            'unit_price' => $product_variation->getPrice(),
        ]);
        $order_item->save();
        //create user first with email
        //create order
        $user = user_load_by_mail($form_state->getValue('email'));
        if(!$user){
            $user = \Drupal\user\Entity\User::create(array(
                'name' => $form_state->getValue('email'),
                'mail' => $form_state->getValue('email'),
                'pass' => 'hkduPass@123',
                'field_first_name' => $form_state->getValue('fname'),
                'field_last_name' => $form_state->getValue('lname'),
                'status' => 1,
            ));
            $user -> save();
        }
        // Create the billing profile.
        $profile = \Drupal\profile\Entity\Profile::create([
            'type' => 'customer',
            'uid' =>$user->id(),
        ]);
        $profile->save();

        // Next, we create the order.
        $order = \Drupal\commerce_order\Entity\Order::create([
            'type' => 'default',
            'state' => 'completed',
            'mail' => $user->getEmail(),
            'uid' => $user->id(),
            'ip_address' => '127.0.0.1',
            'order_number' => $this->getOrderNumber(),
            'billing_profile' => $profile,
            'store_id' => 1,
            'order_items' => [$order_item],
            'placed' => time(),
            'total_paid__number' => $product_variation->getPrice()->getNumber(),
            'total_paid__currency_code' => 'HKD'
        ]);
        $order->save();
        //create tracking
        $tracking = \Drupal\event_tracking\Entity\EventTracking::create([
            'name'=>'Order::'.$user->getAccountName().'::'.$order->id(),
            'field_user' => $user->id(),
            'field_order' => $order->id(),
            'field_event' => $form_state->getValue('event'),
            'uid' => $user->id(),
        ]);
        $tracking->save();
        \Drupal::messenger()->addMessage('Enrolled success for '.$user->getEmail());
    }

    public function getEvents(){
        $ids = \Drupal::entityQuery('event')
            ->condition('status', 1)
            ->sort('name','ASC')
            ->execute();
        $result = \Drupal\event\Entity\Event::loadMultiple($ids);
        $events = [];
        foreach ($result as $event){
            if(!checkExpiredEvent($event)){
                $events[$event->id()] = $event->getName();
            }

        }
        return $event;
    }
    public function getEventProduct($event_id){
        $ids = \Drupal::entityQuery('commerce_product')
            ->condition('status', 1)
            ->condition('type', 'default')
            ->condition('field_event',$event_id)
            ->execute();
        $result = \Drupal\commerce_product\Entity\Product::loadMultiple($ids);
        if($result){
            return reset($result);
        }else{
            return false;
        }
    }

    public function getOrderNumber(){
        $ids = \Drupal::entityQuery('commerce_order')
            ->condition('type', 'default')
            ->sort('order_number','DESC')
            ->range(0,1)
            ->execute();
        $result = \Drupal\commerce_order\Entity\Order::loadMultiple($ids);
        $product = reset($result);
        return ($product->getOrderNumber() + 1);
    }

    public function checkUserExist(){

    }

}
