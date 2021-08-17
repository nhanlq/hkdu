<?php

namespace Drupal\notify;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\notify\Entity\UserNotifyInterface;

/**
 * Defines the storage handler class for User notify entities.
 *
 * This extends the base storage class, adding required special handling for
 * User notify entities.
 *
 * @ingroup notify
 */
interface UserNotifyStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of User notify revision IDs for a specific User notify.
   *
   * @param \Drupal\notify\Entity\UserNotifyInterface $entity
   *   The User notify entity.
   *
   * @return int[]
   *   User notify revision IDs (in ascending order).
   */
  public function revisionIds(UserNotifyInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as User notify author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   User notify revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\notify\Entity\UserNotifyInterface $entity
   *   The User notify entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(UserNotifyInterface $entity);

  /**
   * Unsets the language for all User notify with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
