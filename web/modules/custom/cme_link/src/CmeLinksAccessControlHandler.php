<?php

namespace Drupal\cme_link;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the CME Links entity.
 *
 * @see \Drupal\cme_link\Entity\CmeLinks.
 */
class CmeLinksAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\cme_link\Entity\CmeLinksInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished cme links entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published cme links entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit cme links entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete cme links entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add cme links entities');
  }


}
