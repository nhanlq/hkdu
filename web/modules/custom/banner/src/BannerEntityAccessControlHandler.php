<?php

namespace Drupal\banner;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Banner entity entity.
 *
 * @see \Drupal\banner\Entity\BannerEntity.
 */
class BannerEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\banner\Entity\BannerEntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished banner entity entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published banner entity entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit banner entity entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete banner entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add banner entity entities');
  }


}
