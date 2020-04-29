<?php

namespace Drupal\hospital;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\hospital\Entity\HospitalEntityInterface;

/**
 * Defines the storage handler class for Hospital entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Hospital entity entities.
 *
 * @ingroup hospital
 */
class HospitalEntityStorage extends SqlContentEntityStorage implements HospitalEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(HospitalEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {hospital_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {hospital_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(HospitalEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {hospital_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('hospital_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
