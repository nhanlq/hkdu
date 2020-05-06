<?php

namespace Drupal\event\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\commerce_order\Event\OrderEvent;
use Drupal\commerce_order\Event\OrderEvents;

/**
 * Subscribes to order events to syncronize orders with their payment intents.
 *
 * Payment intents contain the amount which should be charged during a
 * transaction. When a payment intent is confirmed server or client side, that
 * amount is what is charged. To ensure a proper charge amount, we must update
 * the payment intent amount whenever an order is updated.
 */
class EventSuccessSubscriber implements EventSubscriberInterface{

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The intent IDs that need updating.
   *
   * @var int[]
   */
  protected $updateList = [];

  /**
   * Constructs a new OrderEventsSubscriber object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      OrderEvents::ORDER_UPDATE => 'eventOnOrderUpdate',
    ];
  }


  /**
   * Ensures the Stripe payment intent is up to date.
   *
   * @param \Drupal\commerce_order\Event\OrderEvent $event
   *   The event.
   */
  public function eventOnOrderUpdate(OrderEvent $event) {

    $order = $event->getOrder();
    $state = $order->getState();
    $status = $state->getId();
    $user = \Drupal:: currentUser();
    if($status == 'completed'){
        foreach($order->getItems() as $key => $item){
            $product_item = $item->getPurchasedEntity();
            break;
        }
        $product_item = $product_item->toArray();
        $product = \Drupal\commerce_product\Entity\Product::load($product_item['product_id'][0]['target_id']);
        $event = false;
        $cme_event = false;
        if($product->get('field_event')){
            $event = $product->get('field_event')->target_id;
        }
        if($product->get('field_cme_event')){
            $cme_event = $product->get('field_cme_event')->target_id;
            $cme_event_entity = \Drupal\cme_event\Entity\CmeEvent::load($cme_event);
            $score = \Drupal\cme_score\Entity\Score::create([
                'name' => 'Event Score of event ' . $cme_event_entity->getName() . ' of User ' . $user->getDisplayName(),
                'field_score' => 0,
                'field_user' => $user->id(),
                'field_event' => $cme_event_entity->id(),
                'field_attendance' => 0,
                'uid' => $user->id()
            ]);
            $score->save();
        }
        $event_tracking = \Drupal\event_tracking\Entity\EventTracking::create([
            'name' => 'Order::'.$user->getAccountName().'::'.$order->getOrderNumber(),
            'field_event' => isset($event) ? $event : null,
            'field_cme_event' => isset($cme_event) ? $cme_event : null,
            'field_order' => $order->getOrderNumber(),
            'field_user' => $user->id(),
            'created' => time(),
            'uid' => $user->id(),
        ]);
        $event_tracking->save();
    }

  }
}
