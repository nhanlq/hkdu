<?php

namespace Drupal\doctor;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\doctor\Entity\DoctorEntityInterface;

/**
 * Defines the storage handler class for Doctor entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Doctor entity entities.
 *
 * @ingroup doctor
 */
interface DoctorEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Doctor entity revision IDs for a specific Doctor entity.
   *
   * @param \Drupal\doctor\Entity\DoctorEntityInterface $entity
   *   The Doctor entity entity.
   *
   * @return int[]
   *   Doctor entity revision IDs (in ascending order).
   */
  public function revisionIds(DoctorEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Doctor entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Doctor entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\doctor\Entity\DoctorEntityInterface $entity
   *   The Doctor entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(DoctorEntityInterface $entity);

  /**
   * Unsets the language for all Doctor entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
