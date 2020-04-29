<?php

namespace Drupal\media_release;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\media_release\Entity\MediaEntityInterface;

/**
 * Defines the storage handler class for Media entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Media entity entities.
 *
 * @ingroup media_release
 */
interface MediaEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Media entity revision IDs for a specific Media entity.
   *
   * @param \Drupal\media_release\Entity\MediaEntityInterface $entity
   *   The Media entity entity.
   *
   * @return int[]
   *   Media entity revision IDs (in ascending order).
   */
  public function revisionIds(MediaEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Media entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Media entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\media_release\Entity\MediaEntityInterface $entity
   *   The Media entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(MediaEntityInterface $entity);

  /**
   * Unsets the language for all Media entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
