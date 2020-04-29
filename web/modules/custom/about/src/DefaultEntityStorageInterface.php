<?php

namespace Drupal\about;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\about\Entity\DefaultEntityInterface;

/**
 * Defines the storage handler class for Default entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Default entity entities.
 *
 * @ingroup about
 */
interface DefaultEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Default entity revision IDs for a specific Default entity.
   *
   * @param \Drupal\about\Entity\DefaultEntityInterface $entity
   *   The Default entity entity.
   *
   * @return int[]
   *   Default entity revision IDs (in ascending order).
   */
  public function revisionIds(DefaultEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Default entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Default entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\about\Entity\DefaultEntityInterface $entity
   *   The Default entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(DefaultEntityInterface $entity);

  /**
   * Unsets the language for all Default entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
