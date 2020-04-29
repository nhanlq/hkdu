<?php

namespace Drupal\download;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Download entity.
 *
 * @see \Drupal\download\Entity\Download.
 */
class DownloadAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\download\Entity\DownloadInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished download entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published download entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit download entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete download entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add download entities');
  }


}
