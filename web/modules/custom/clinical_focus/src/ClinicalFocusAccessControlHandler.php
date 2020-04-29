<?php

namespace Drupal\clinical_focus;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Clinical focus entity.
 *
 * @see \Drupal\clinical_focus\Entity\ClinicalFocus.
 */
class ClinicalFocusAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\clinical_focus\Entity\ClinicalFocusInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished clinical focus entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published clinical focus entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit clinical focus entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete clinical focus entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add clinical focus entities');
  }


}
