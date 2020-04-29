<?php

namespace Drupal\drug_search;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Drug search entity.
 *
 * @see \Drupal\drug_search\Entity\DrugSearch.
 */
class DrugSearchAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\drug_search\Entity\DrugSearchInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished drug search entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published drug search entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit drug search entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete drug search entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add drug search entities');
  }


}
