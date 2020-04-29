<?php

namespace Drupal\quiz_tracking;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Quiz tracking entity.
 *
 * @see \Drupal\quiz_tracking\Entity\QuizTracking.
 */
class QuizTrackingAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\quiz_tracking\Entity\QuizTrackingInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished quiz tracking entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published quiz tracking entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit quiz tracking entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete quiz tracking entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add quiz tracking entities');
  }


}
