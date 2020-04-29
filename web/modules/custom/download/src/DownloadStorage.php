<?php

namespace Drupal\download;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\download\Entity\DownloadInterface;

/**
 * Defines the storage handler class for Download entities.
 *
 * This extends the base storage class, adding required special handling for
 * Download entities.
 *
 * @ingroup download
 */
class DownloadStorage extends SqlContentEntityStorage implements DownloadStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(DownloadInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {download_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {download_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(DownloadInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {download_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('download_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
