<?php

namespace Drupal\tools;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\tools\Entity\ToolsInterface;

/**
 * Defines the storage handler class for Tools entities.
 *
 * This extends the base storage class, adding required special handling for
 * Tools entities.
 *
 * @ingroup tools
 */
interface ToolsStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Tools revision IDs for a specific Tools.
   *
   * @param \Drupal\tools\Entity\ToolsInterface $entity
   *   The Tools entity.
   *
   * @return int[]
   *   Tools revision IDs (in ascending order).
   */
  public function revisionIds(ToolsInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Tools author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Tools revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\tools\Entity\ToolsInterface $entity
   *   The Tools entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ToolsInterface $entity);

  /**
   * Unsets the language for all Tools with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
