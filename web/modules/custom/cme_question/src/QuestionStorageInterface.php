<?php

namespace Drupal\cme_question;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\cme_question\Entity\QuestionInterface;

/**
 * Defines the storage handler class for Question entities.
 *
 * This extends the base storage class, adding required special handling for
 * Question entities.
 *
 * @ingroup cme_question
 */
interface QuestionStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Question revision IDs for a specific Question.
   *
   * @param \Drupal\cme_question\Entity\QuestionInterface $entity
   *   The Question entity.
   *
   * @return int[]
   *   Question revision IDs (in ascending order).
   */
  public function revisionIds(QuestionInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Question author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Question revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\cme_question\Entity\QuestionInterface $entity
   *   The Question entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(QuestionInterface $entity);

  /**
   * Unsets the language for all Question with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
