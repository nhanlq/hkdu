<?php

namespace Drupal\cme_link;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\cme_link\Entity\CmeLinksInterface;

/**
 * Defines the storage handler class for CME Links entities.
 *
 * This extends the base storage class, adding required special handling for
 * CME Links entities.
 *
 * @ingroup cme_link
 */
interface CmeLinksStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of CME Links revision IDs for a specific CME Links.
   *
   * @param \Drupal\cme_link\Entity\CmeLinksInterface $entity
   *   The CME Links entity.
   *
   * @return int[]
   *   CME Links revision IDs (in ascending order).
   */
  public function revisionIds(CmeLinksInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as CME Links author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   CME Links revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\cme_link\Entity\CmeLinksInterface $entity
   *   The CME Links entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(CmeLinksInterface $entity);

  /**
   * Unsets the language for all CME Links with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
