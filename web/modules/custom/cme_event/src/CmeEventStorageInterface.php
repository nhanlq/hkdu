<?php

namespace Drupal\cme_event;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\cme_event\Entity\CmeEventInterface;

/**
 * Defines the storage handler class for CME Event entities.
 *
 * This extends the base storage class, adding required special handling for
 * CME Event entities.
 *
 * @ingroup cme_event
 */
interface CmeEventStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of CME Event revision IDs for a specific CME Event.
   *
   * @param \Drupal\cme_event\Entity\CmeEventInterface $entity
   *   The CME Event entity.
   *
   * @return int[]
   *   CME Event revision IDs (in ascending order).
   */
  public function revisionIds(CmeEventInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as CME Event author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   CME Event revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\cme_event\Entity\CmeEventInterface $entity
   *   The CME Event entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(CmeEventInterface $entity);

  /**
   * Unsets the language for all CME Event with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
