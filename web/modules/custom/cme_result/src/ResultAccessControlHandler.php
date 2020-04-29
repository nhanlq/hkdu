<?php

namespace Drupal\cme_result;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Result entity.
 *
 * @see \Drupal\cme_result\Entity\Result.
 */
class ResultAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\cme_result\Entity\ResultInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished result entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published result entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit result entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete result entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add result entities');
  }


}
