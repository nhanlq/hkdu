<?php

namespace Drupal\drug_search;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface DrugSearchStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Drug search revision IDs for a specific Drug search.
   *
   * @param \Drupal\drug_search\Entity\DrugSearchInterface $entity
   *   The Drug search entity.
   *
   * @return int[]
   *   Drug search revision IDs (in ascending order).
   */
  public function revisionIds(DrugSearchInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Drug search author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Drug search revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\drug_search\Entity\DrugSearchInterface $entity
   *   The Drug search entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(DrugSearchInterface $entity);

  /**
   * Unsets the language for all Drug search with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
