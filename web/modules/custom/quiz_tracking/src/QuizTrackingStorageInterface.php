<?php

namespace Drupal\quiz_tracking;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface QuizTrackingStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Quiz tracking revision IDs for a specific Quiz tracking.
   *
   * @param \Drupal\quiz_tracking\Entity\QuizTrackingInterface $entity
   *   The Quiz tracking entity.
   *
   * @return int[]
   *   Quiz tracking revision IDs (in ascending order).
   */
  public function revisionIds(QuizTrackingInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Quiz tracking author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Quiz tracking revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\quiz_tracking\Entity\QuizTrackingInterface $entity
   *   The Quiz tracking entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(QuizTrackingInterface $entity);

  /**
   * Unsets the language for all Quiz tracking with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
