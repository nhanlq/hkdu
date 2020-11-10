<?php

namespace Drupal\payment_upload;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Payment upload entity.
 *
 * @see \Drupal\payment_upload\Entity\PaymentUpload.
 */
class PaymentUploadAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\payment_upload\Entity\PaymentUploadInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished payment upload entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published payment upload entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit payment upload entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete payment upload entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add payment upload entities');
  }


}
