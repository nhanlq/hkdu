<?php

namespace Drupal\ads_tracking;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Tracking entity.
 *
 * @see \Drupal\ads_tracking\Entity\Tracking.
 */
class TrackingAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\ads_tracking\Entity\TrackingInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished tracking entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published tracking entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit tracking entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete tracking entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add tracking entities');
  }


}
