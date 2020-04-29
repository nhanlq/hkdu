<?php

namespace Drupal\event_tracking;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\event_tracking\Entity\EventTrackingInterface;

/**
 * Defines the storage handler class for Event tracking entities.
 *
 * This extends the base storage class, adding required special handling for
 * Event tracking entities.
 *
 * @ingroup event_tracking
 */
class EventTrackingStorage extends SqlContentEntityStorage implements EventTrackingStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(EventTrackingInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {event_tracking_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {event_tracking_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(EventTrackingInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {event_tracking_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('event_tracking_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
