<?php

namespace Drupal\download;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\download\Entity\DownloadInterface;

/**
 * Defines the storage handler class for Download entities.
 *
 * This extends the base storage class, adding required special handling for
 * Download entities.
 *
 * @ingroup download
 */
interface DownloadStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Download revision IDs for a specific Download.
   *
   * @param \Drupal\download\Entity\DownloadInterface $entity
   *   The Download entity.
   *
   * @return int[]
   *   Download revision IDs (in ascending order).
   */
  public function revisionIds(DownloadInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Download author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Download revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\download\Entity\DownloadInterface $entity
   *   The Download entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(DownloadInterface $entity);

  /**
   * Unsets the language for all Download with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
