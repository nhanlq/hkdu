<?php

namespace Drupal\cme_score;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface ScoreStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Score revision IDs for a specific Score.
   *
   * @param \Drupal\cme_score\Entity\ScoreInterface $entity
   *   The Score entity.
   *
   * @return int[]
   *   Score revision IDs (in ascending order).
   */
  public function revisionIds(ScoreInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Score author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Score revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\cme_score\Entity\ScoreInterface $entity
   *   The Score entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ScoreInterface $entity);

  /**
   * Unsets the language for all Score with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
