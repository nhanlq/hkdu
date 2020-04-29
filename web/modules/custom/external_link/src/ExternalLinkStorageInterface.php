<?php

namespace Drupal\external_link;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\external_link\Entity\ExternalLinkInterface;

/**
 * Defines the storage handler class for External link entities.
 *
 * This extends the base storage class, adding required special handling for
 * External link entities.
 *
 * @ingroup external_link
 */
interface ExternalLinkStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of External link revision IDs for a specific External link.
   *
   * @param \Drupal\external_link\Entity\ExternalLinkInterface $entity
   *   The External link entity.
   *
   * @return int[]
   *   External link revision IDs (in ascending order).
   */
  public function revisionIds(ExternalLinkInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as External link author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   External link revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\external_link\Entity\ExternalLinkInterface $entity
   *   The External link entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ExternalLinkInterface $entity);

  /**
   * Unsets the language for all External link with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
