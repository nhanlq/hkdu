<?php

namespace Drupal\hospital;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\hospital\Entity\HospitalEntityInterface;

/**
 * Defines the storage handler class for Hospital entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Hospital entity entities.
 *
 * @ingroup hospital
 */
interface HospitalEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Hospital entity revision IDs for a specific Hospital entity.
   *
   * @param \Drupal\hospital\Entity\HospitalEntityInterface $entity
   *   The Hospital entity entity.
   *
   * @return int[]
   *   Hospital entity revision IDs (in ascending order).
   */
  public function revisionIds(HospitalEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Hospital entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Hospital entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\hospital\Entity\HospitalEntityInterface $entity
   *   The Hospital entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(HospitalEntityInterface $entity);

  /**
   * Unsets the language for all Hospital entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
