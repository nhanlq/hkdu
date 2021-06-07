<?php

namespace Drupal\user_point;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\user_point\Entity\PointInterface;

/**
 * Defines the storage handler class for Point entities.
 *
 * This extends the base storage class, adding required special handling for
 * Point entities.
 *
 * @ingroup user_point
 */
interface PointStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Point revision IDs for a specific Point.
   *
   * @param \Drupal\user_point\Entity\PointInterface $entity
   *   The Point entity.
   *
   * @return int[]
   *   Point revision IDs (in ascending order).
   */
  public function revisionIds(PointInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Point author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Point revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\user_point\Entity\PointInterface $entity
   *   The Point entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(PointInterface $entity);

  /**
   * Unsets the language for all Point with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
