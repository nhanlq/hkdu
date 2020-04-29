<?php

namespace Drupal\hospital;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Hospital entity entity.
 *
 * @see \Drupal\hospital\Entity\HospitalEntity.
 */
class HospitalEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\hospital\Entity\HospitalEntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished hospital entity entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published hospital entity entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit hospital entity entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete hospital entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add hospital entity entities');
  }


}
