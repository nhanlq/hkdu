<?php

namespace Drupal\healthy;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Healthy entity.
 *
 * @see \Drupal\healthy\Entity\Healthy.
 */
class HealthyAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\healthy\Entity\HealthyInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished healthy entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published healthy entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit healthy entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete healthy entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add healthy entities');
  }


}
