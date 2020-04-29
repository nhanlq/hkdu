<?php

namespace Drupal\cme_event_organizer;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\cme_event_organizer\Entity\EventOrgInterface;

/**
 * Defines the storage handler class for Event Organizer entities.
 *
 * This extends the base storage class, adding required special handling for
 * Event Organizer entities.
 *
 * @ingroup cme_event_organizer
 */
interface EventOrgStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Event Organizer revision IDs for a specific Event Organizer.
   *
   * @param \Drupal\cme_event_organizer\Entity\EventOrgInterface $entity
   *   The Event Organizer entity.
   *
   * @return int[]
   *   Event Organizer revision IDs (in ascending order).
   */
  public function revisionIds(EventOrgInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Event Organizer author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Event Organizer revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\cme_event_organizer\Entity\EventOrgInterface $entity
   *   The Event Organizer entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(EventOrgInterface $entity);

  /**
   * Unsets the language for all Event Organizer with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
