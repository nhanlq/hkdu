<?php

namespace Drupal\public_links;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\public_links\Entity\PublicLinksInterface;

/**
 * Defines the storage handler class for Public links entities.
 *
 * This extends the base storage class, adding required special handling for
 * Public links entities.
 *
 * @ingroup public_links
 */
interface PublicLinksStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Public links revision IDs for a specific Public links.
   *
   * @param \Drupal\public_links\Entity\PublicLinksInterface $entity
   *   The Public links entity.
   *
   * @return int[]
   *   Public links revision IDs (in ascending order).
   */
  public function revisionIds(PublicLinksInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Public links author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Public links revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\public_links\Entity\PublicLinksInterface $entity
   *   The Public links entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(PublicLinksInterface $entity);

  /**
   * Unsets the language for all Public links with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
