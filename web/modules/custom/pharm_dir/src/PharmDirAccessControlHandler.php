<?php

namespace Drupal\pharm_dir;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Pharm dir entity.
 *
 * @see \Drupal\pharm_dir\Entity\PharmDir.
 */
class PharmDirAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\pharm_dir\Entity\PharmDirInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished pharm dir entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published pharm dir entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit pharm dir entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete pharm dir entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add pharm dir entities');
  }


}
