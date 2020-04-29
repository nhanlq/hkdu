<?php

namespace Drupal\cme_event_organizer;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\cme_event_organizer\Entity\EventOrgInterface;

/**
 * Defines the storage handler class for Event Organizer entities.
 *
 * This extends the base storage class, adding required special handling for
 * Event Organizer entities.
 *
 * @ingroup cme_event_organizer
 */
class EventOrgStorage extends SqlContentEntityStorage implements EventOrgStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(EventOrgInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {event_org_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {event_org_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(EventOrgInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {event_org_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('event_org_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
