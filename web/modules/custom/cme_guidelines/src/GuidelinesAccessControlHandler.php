<?php

namespace Drupal\cme_guidelines;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Guidelines entity.
 *
 * @see \Drupal\cme_guidelines\Entity\Guidelines.
 */
class GuidelinesAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\cme_guidelines\Entity\GuidelinesInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished guidelines entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published guidelines entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit guidelines entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete guidelines entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add guidelines entities');
  }


}
