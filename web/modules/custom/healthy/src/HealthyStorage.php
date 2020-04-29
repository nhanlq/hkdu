<?php

namespace Drupal\healthy;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\healthy\Entity\HealthyInterface;

/**
 * Defines the storage handler class for Healthy entities.
 *
 * This extends the base storage class, adding required special handling for
 * Healthy entities.
 *
 * @ingroup healthy
 */
class HealthyStorage extends SqlContentEntityStorage implements HealthyStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(HealthyInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {healthy_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {healthy_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(HealthyInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {healthy_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('healthy_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
