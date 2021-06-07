<?php

namespace Drupal\user_point;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\user_point\Entity\PointInterface;

/**
 * Defines the storage handler class for Point entities.
 *
 * This extends the base storage class, adding required special handling for
 * Point entities.
 *
 * @ingroup user_point
 */
class PointStorage extends SqlContentEntityStorage implements PointStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(PointInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {point_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {point_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(PointInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {point_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('point_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
