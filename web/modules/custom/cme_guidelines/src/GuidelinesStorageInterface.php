<?php

namespace Drupal\cme_guidelines;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\cme_guidelines\Entity\GuidelinesInterface;

/**
 * Defines the storage handler class for Guidelines entities.
 *
 * This extends the base storage class, adding required special handling for
 * Guidelines entities.
 *
 * @ingroup cme_guidelines
 */
interface GuidelinesStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Guidelines revision IDs for a specific Guidelines.
   *
   * @param \Drupal\cme_guidelines\Entity\GuidelinesInterface $entity
   *   The Guidelines entity.
   *
   * @return int[]
   *   Guidelines revision IDs (in ascending order).
   */
  public function revisionIds(GuidelinesInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Guidelines author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Guidelines revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\cme_guidelines\Entity\GuidelinesInterface $entity
   *   The Guidelines entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(GuidelinesInterface $entity);

  /**
   * Unsets the language for all Guidelines with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
