<?php

namespace Drupal\doctor;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Doctor entity entity.
 *
 * @see \Drupal\doctor\Entity\DoctorEntity.
 */
class DoctorEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\doctor\Entity\DoctorEntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished doctor entity entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published doctor entity entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit doctor entity entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete doctor entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add doctor entity entities');
  }


}
