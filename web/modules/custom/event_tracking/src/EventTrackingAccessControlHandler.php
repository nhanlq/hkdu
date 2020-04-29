<?php

namespace Drupal\event_tracking;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Event tracking entity.
 *
 * @see \Drupal\event_tracking\Entity\EventTracking.
 */
class EventTrackingAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\event_tracking\Entity\EventTrackingInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished event tracking entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published event tracking entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit event tracking entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete event tracking entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add event tracking entities');
  }


}
