<?php

namespace Drupal\media_release;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Media entity entity.
 *
 * @see \Drupal\media_release\Entity\MediaEntity.
 */
class MediaEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\media_release\Entity\MediaEntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished media entity entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published media entity entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit media entity entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete media entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add media entity entities');
  }


}
