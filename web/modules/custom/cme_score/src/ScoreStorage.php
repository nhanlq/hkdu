<?php

namespace Drupal\cme_score;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\cme_score\Entity\ScoreInterface;

/**
 * Defines the storage handler class for Score entities.
 *
 * This extends the base storage class, adding required special handling for
 * Score entities.
 *
 * @ingroup cme_score
 */
class ScoreStorage extends SqlContentEntityStorage implements ScoreStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(ScoreInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {score_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {score_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(ScoreInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {score_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('score_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
