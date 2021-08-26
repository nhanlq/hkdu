<?php

namespace Drupal\notify;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\notify\Entity\UserNotifyInterface;

/**
 * Defines the storage handler class for User notify entities.
 *
 * This extends the base storage class, adding required special handling for
 * User notify entities.
 *
 * @ingroup notify
 */
class UserNotifyStorage extends SqlContentEntityStorage implements UserNotifyStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(UserNotifyInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {user_notify_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {user_notify_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(UserNotifyInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {user_notify_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('user_notify_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
