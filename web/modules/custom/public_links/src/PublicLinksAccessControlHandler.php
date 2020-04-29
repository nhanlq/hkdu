<?php

namespace Drupal\public_links;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Public links entity.
 *
 * @see \Drupal\public_links\Entity\PublicLinks.
 */
class PublicLinksAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\public_links\Entity\PublicLinksInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished public links entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published public links entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit public links entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete public links entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add public links entities');
  }


}
