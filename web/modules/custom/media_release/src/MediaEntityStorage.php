<?php

namespace Drupal\media_release;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\media_release\Entity\MediaEntityInterface;

/**
 * Defines the storage handler class for Media entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Media entity entities.
 *
 * @ingroup media_release
 */
class MediaEntityStorage extends SqlContentEntityStorage implements MediaEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(MediaEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {media_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {media_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(MediaEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {media_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('media_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
