<?php

namespace Drupal\gallery;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Gallery entity.
 *
 * @see \Drupal\gallery\Entity\Gallery.
 */
class GalleryAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\gallery\Entity\GalleryInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished gallery entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published gallery entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit gallery entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete gallery entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add gallery entities');
  }


}
