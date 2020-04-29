<?php

namespace Drupal\drug_news;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\drug_news\Entity\DrugNewsInterface;

/**
 * Defines the storage handler class for Drug news entities.
 *
 * This extends the base storage class, adding required special handling for
 * Drug news entities.
 *
 * @ingroup drug_news
 */
class DrugNewsStorage extends SqlContentEntityStorage implements DrugNewsStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(DrugNewsInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {drug_news_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {drug_news_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(DrugNewsInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {drug_news_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('drug_news_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
