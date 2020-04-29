<?php

namespace Drupal\drug_news;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Drug news entity.
 *
 * @see \Drupal\drug_news\Entity\DrugNews.
 */
class DrugNewsAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\drug_news\Entity\DrugNewsInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished drug news entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published drug news entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit drug news entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete drug news entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add drug news entities');
  }


}
