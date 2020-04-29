<?php

namespace Drupal\cme_event;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the CME Event entity.
 *
 * @see \Drupal\cme_event\Entity\CmeEvent.
 */
class CmeEventAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\cme_event\Entity\CmeEventInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished cme event entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published cme event entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit cme event entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete cme event entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add cme event entities');
  }


}
