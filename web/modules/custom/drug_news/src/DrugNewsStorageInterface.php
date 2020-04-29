<?php

namespace Drupal\drug_news;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface DrugNewsStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Drug news revision IDs for a specific Drug news.
   *
   * @param \Drupal\drug_news\Entity\DrugNewsInterface $entity
   *   The Drug news entity.
   *
   * @return int[]
   *   Drug news revision IDs (in ascending order).
   */
  public function revisionIds(DrugNewsInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Drug news author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Drug news revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\drug_news\Entity\DrugNewsInterface $entity
   *   The Drug news entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(DrugNewsInterface $entity);

  /**
   * Unsets the language for all Drug news with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
