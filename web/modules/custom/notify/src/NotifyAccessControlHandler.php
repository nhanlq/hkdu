<?php

namespace Drupal\notify;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Notify entity.
 *
 * @see \Drupal\notify\Entity\Notify.
 */
class NotifyAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\notify\Entity\NotifyInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished notify entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published notify entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit notify entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete notify entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add notify entities');
  }


}
