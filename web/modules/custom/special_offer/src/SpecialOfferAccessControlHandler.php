<?php

namespace Drupal\special_offer;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Special offer entity.
 *
 * @see \Drupal\special_offer\Entity\SpecialOffer.
 */
class SpecialOfferAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\special_offer\Entity\SpecialOfferInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished special offer entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published special offer entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit special offer entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete special offer entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add special offer entities');
  }


}
