<?php

namespace Drupal\banner;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\banner\Entity\BannerEntityInterface;

/**
 * Defines the storage handler class for Banner entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Banner entity entities.
 *
 * @ingroup banner
 */
interface BannerEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Banner entity revision IDs for a specific Banner entity.
   *
   * @param \Drupal\banner\Entity\BannerEntityInterface $entity
   *   The Banner entity entity.
   *
   * @return int[]
   *   Banner entity revision IDs (in ascending order).
   */
  public function revisionIds(BannerEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Banner entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Banner entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\banner\Entity\BannerEntityInterface $entity
   *   The Banner entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(BannerEntityInterface $entity);

  /**
   * Unsets the language for all Banner entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
