<?php

namespace Drupal\advert;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Ads entity.
 *
 * @see \Drupal\advert\Entity\Ads.
 */
class AdsAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\advert\Entity\AdsInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished ads entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published ads entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit ads entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete ads entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add ads entities');
  }


}
