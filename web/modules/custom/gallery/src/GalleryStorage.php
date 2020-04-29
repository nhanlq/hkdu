<?php

namespace Drupal\gallery;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\gallery\Entity\GalleryInterface;

/**
 * Defines the storage handler class for Gallery entities.
 *
 * This extends the base storage class, adding required special handling for
 * Gallery entities.
 *
 * @ingroup gallery
 */
class GalleryStorage extends SqlContentEntityStorage implements GalleryStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(GalleryInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {gallery_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {gallery_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(GalleryInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {gallery_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('gallery_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
