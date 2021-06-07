<?php

namespace Drupal\user_point;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Point entity.
 *
 * @see \Drupal\user_point\Entity\Point.
 */
class PointAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\user_point\Entity\PointInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished point entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published point entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit point entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete point entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add point entities');
  }


}
