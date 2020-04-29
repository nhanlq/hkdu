<?php

namespace Drupal\doctor;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\doctor\Entity\DoctorEntityInterface;

/**
 * Defines the storage handler class for Doctor entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Doctor entity entities.
 *
 * @ingroup doctor
 */
class DoctorEntityStorage extends SqlContentEntityStorage implements DoctorEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(DoctorEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {doctor_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {doctor_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(DoctorEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {doctor_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('doctor_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
