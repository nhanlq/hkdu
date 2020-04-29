<?php

namespace Drupal\cme_event;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\cme_event\Entity\CmeEventInterface;

/**
 * Defines the storage handler class for CME Event entities.
 *
 * This extends the base storage class, adding required special handling for
 * CME Event entities.
 *
 * @ingroup cme_event
 */
class CmeEventStorage extends SqlContentEntityStorage implements CmeEventStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(CmeEventInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {cme_event_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {cme_event_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(CmeEventInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {cme_event_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('cme_event_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
