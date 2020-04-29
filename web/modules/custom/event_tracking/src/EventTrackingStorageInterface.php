<?php

namespace Drupal\event_tracking;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\event_tracking\Entity\EventTrackingInterface;

/**
 * Defines the storage handler class for Event tracking entities.
 *
 * This extends the base storage class, adding required special handling for
 * Event tracking entities.
 *
 * @ingroup event_tracking
 */
interface EventTrackingStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Event tracking revision IDs for a specific Event tracking.
   *
   * @param \Drupal\event_tracking\Entity\EventTrackingInterface $entity
   *   The Event tracking entity.
   *
   * @return int[]
   *   Event tracking revision IDs (in ascending order).
   */
  public function revisionIds(EventTrackingInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Event tracking author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Event tracking revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\event_tracking\Entity\EventTrackingInterface $entity
   *   The Event tracking entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(EventTrackingInterface $entity);

  /**
   * Unsets the language for all Event tracking with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
