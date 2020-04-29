<?php

namespace Drupal\pharm_dir;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\pharm_dir\Entity\PharmDirInterface;

/**
 * Defines the storage handler class for Pharm dir entities.
 *
 * This extends the base storage class, adding required special handling for
 * Pharm dir entities.
 *
 * @ingroup pharm_dir
 */
class PharmDirStorage extends SqlContentEntityStorage implements PharmDirStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(PharmDirInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {pharm_dir_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {pharm_dir_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(PharmDirInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {pharm_dir_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('pharm_dir_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
