<?php

namespace Drupal\cme_result;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\cme_result\Entity\ResultInterface;

/**
 * Defines the storage handler class for Result entities.
 *
 * This extends the base storage class, adding required special handling for
 * Result entities.
 *
 * @ingroup cme_result
 */
interface ResultStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Result revision IDs for a specific Result.
   *
   * @param \Drupal\cme_result\Entity\ResultInterface $entity
   *   The Result entity.
   *
   * @return int[]
   *   Result revision IDs (in ascending order).
   */
  public function revisionIds(ResultInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Result author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Result revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\cme_result\Entity\ResultInterface $entity
   *   The Result entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ResultInterface $entity);

  /**
   * Unsets the language for all Result with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
