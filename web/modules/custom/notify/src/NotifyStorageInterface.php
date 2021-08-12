<?php

namespace Drupal\notify;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\notify\Entity\NotifyInterface;

/**
 * Defines the storage handler class for Notify entities.
 *
 * This extends the base storage class, adding required special handling for
 * Notify entities.
 *
 * @ingroup notify
 */
interface NotifyStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Notify revision IDs for a specific Notify.
   *
   * @param \Drupal\notify\Entity\NotifyInterface $entity
   *   The Notify entity.
   *
   * @return int[]
   *   Notify revision IDs (in ascending order).
   */
  public function revisionIds(NotifyInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Notify author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Notify revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\notify\Entity\NotifyInterface $entity
   *   The Notify entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(NotifyInterface $entity);

  /**
   * Unsets the language for all Notify with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
