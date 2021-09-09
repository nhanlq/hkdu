<?php

namespace Drupal\ads_tracking;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\ads_tracking\Entity\TrackingInterface;

/**
 * Defines the storage handler class for Tracking entities.
 *
 * This extends the base storage class, adding required special handling for
 * Tracking entities.
 *
 * @ingroup ads_tracking
 */
class TrackingStorage extends SqlContentEntityStorage implements TrackingStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(TrackingInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {tracking_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {tracking_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(TrackingInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {tracking_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('tracking_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
