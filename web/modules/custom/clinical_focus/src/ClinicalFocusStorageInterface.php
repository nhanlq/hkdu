<?php

namespace Drupal\clinical_focus;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\clinical_focus\Entity\ClinicalFocusInterface;

/**
 * Defines the storage handler class for Clinical focus entities.
 *
 * This extends the base storage class, adding required special handling for
 * Clinical focus entities.
 *
 * @ingroup clinical_focus
 */
interface ClinicalFocusStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Clinical focus revision IDs for a specific Clinical focus.
   *
   * @param \Drupal\clinical_focus\Entity\ClinicalFocusInterface $entity
   *   The Clinical focus entity.
   *
   * @return int[]
   *   Clinical focus revision IDs (in ascending order).
   */
  public function revisionIds(ClinicalFocusInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Clinical focus author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Clinical focus revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\clinical_focus\Entity\ClinicalFocusInterface $entity
   *   The Clinical focus entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ClinicalFocusInterface $entity);

  /**
   * Unsets the language for all Clinical focus with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
