<?php

namespace Drupal\cme_guidelines;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\cme_guidelines\Entity\GuidelinesInterface;

/**
 * Defines the storage handler class for Guidelines entities.
 *
 * This extends the base storage class, adding required special handling for
 * Guidelines entities.
 *
 * @ingroup cme_guidelines
 */
class GuidelinesStorage extends SqlContentEntityStorage implements GuidelinesStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(GuidelinesInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {guidelines_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {guidelines_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(GuidelinesInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {guidelines_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('guidelines_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
