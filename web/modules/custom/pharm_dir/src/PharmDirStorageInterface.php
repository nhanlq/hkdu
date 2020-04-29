<?php

namespace Drupal\pharm_dir;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\pharm_dir\Entity\PharmDirInterface;

/**
 * Defines the storage handler class for Pharm dir entities.
 *
 * This extends the base storage class, adding required special handling for
 * Pharm dir entities.
 *
 * @ingroup pharm_dir
 */
interface PharmDirStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Pharm dir revision IDs for a specific Pharm dir.
   *
   * @param \Drupal\pharm_dir\Entity\PharmDirInterface $entity
   *   The Pharm dir entity.
   *
   * @return int[]
   *   Pharm dir revision IDs (in ascending order).
   */
  public function revisionIds(PharmDirInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Pharm dir author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Pharm dir revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\pharm_dir\Entity\PharmDirInterface $entity
   *   The Pharm dir entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(PharmDirInterface $entity);

  /**
   * Unsets the language for all Pharm dir with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
