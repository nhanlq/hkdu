<?php

namespace Drupal\quiz_tracking;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\quiz_tracking\Entity\QuizTrackingInterface;

/**
 * Defines the storage handler class for Quiz tracking entities.
 *
 * This extends the base storage class, adding required special handling for
 * Quiz tracking entities.
 *
 * @ingroup quiz_tracking
 */
class QuizTrackingStorage extends SqlContentEntityStorage implements QuizTrackingStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(QuizTrackingInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {quiz_tracking_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {quiz_tracking_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(QuizTrackingInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {quiz_tracking_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('quiz_tracking_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
