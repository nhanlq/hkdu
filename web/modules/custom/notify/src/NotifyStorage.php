<?php

namespace Drupal\notify;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\notify\Entity\NotifyInterface;

/**
 * Defines the storage handler class for Notify entities.
 *
 * This extends the base storage class, adding required special handling for
 * Notify entities.
 *
 * @ingroup notify
 */
class NotifyStorage extends SqlContentEntityStorage implements NotifyStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(NotifyInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {notify_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {notify_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(NotifyInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {notify_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('notify_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
