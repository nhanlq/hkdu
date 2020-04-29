<?php

namespace Drupal\healthy;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\healthy\Entity\HealthyInterface;

/**
 * Defines the storage handler class for Healthy entities.
 *
 * This extends the base storage class, adding required special handling for
 * Healthy entities.
 *
 * @ingroup healthy
 */
interface HealthyStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Healthy revision IDs for a specific Healthy.
   *
   * @param \Drupal\healthy\Entity\HealthyInterface $entity
   *   The Healthy entity.
   *
   * @return int[]
   *   Healthy revision IDs (in ascending order).
   */
  public function revisionIds(HealthyInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Healthy author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Healthy revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\healthy\Entity\HealthyInterface $entity
   *   The Healthy entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(HealthyInterface $entity);

  /**
   * Unsets the language for all Healthy with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
