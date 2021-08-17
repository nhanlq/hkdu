<?php

namespace Drupal\notify;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the User notify entity.
 *
 * @see \Drupal\notify\Entity\UserNotify.
 */
class UserNotifyAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\notify\Entity\UserNotifyInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished user notify entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published user notify entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit user notify entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete user notify entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add user notify entities');
  }


}
