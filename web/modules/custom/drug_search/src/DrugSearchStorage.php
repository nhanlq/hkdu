<?php

namespace Drupal\drug_search;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\drug_search\Entity\DrugSearchInterface;

/**
 * Defines the storage handler class for Drug search entities.
 *
 * This extends the base storage class, adding required special handling for
 * Drug search entities.
 *
 * @ingroup drug_search
 */
class DrugSearchStorage extends SqlContentEntityStorage implements DrugSearchStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(DrugSearchInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {drug_search_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {drug_search_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(DrugSearchInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {drug_search_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('drug_search_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
