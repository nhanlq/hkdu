<?php

namespace Drupal\cme_event_organizer;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Event Organizer entity.
 *
 * @see \Drupal\cme_event_organizer\Entity\EventOrg.
 */
class EventOrgAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\cme_event_organizer\Entity\EventOrgInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished event organizer entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published event organizer entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit event organizer entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete event organizer entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add event organizer entities');
  }


}
