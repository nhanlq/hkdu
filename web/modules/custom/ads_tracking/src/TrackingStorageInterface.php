<?php

namespace Drupal\ads_tracking;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\ads_tracking\Entity\TrackingInterface;

/**
 * Defines the storage handler class for Tracking entities.
 *
 * This extends the base storage class, adding required special handling for
 * Tracking entities.
 *
 * @ingroup ads_tracking
 */
interface TrackingStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Tracking revision IDs for a specific Tracking.
   *
   * @param \Drupal\ads_tracking\Entity\TrackingInterface $entity
   *   The Tracking entity.
   *
   * @return int[]
   *   Tracking revision IDs (in ascending order).
   */
  public function revisionIds(TrackingInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Tracking author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Tracking revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\ads_tracking\Entity\TrackingInterface $entity
   *   The Tracking entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(TrackingInterface $entity);

  /**
   * Unsets the language for all Tracking with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
