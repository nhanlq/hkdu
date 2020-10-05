<?php

namespace Drupal\advert;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\advert\Entity\AdsInterface;

/**
 * Defines the storage handler class for Ads entities.
 *
 * This extends the base storage class, adding required special handling for
 * Ads entities.
 *
 * @ingroup advert
 */
interface AdsStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Ads revision IDs for a specific Ads.
   *
   * @param \Drupal\advert\Entity\AdsInterface $entity
   *   The Ads entity.
   *
   * @return int[]
   *   Ads revision IDs (in ascending order).
   */
  public function revisionIds(AdsInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Ads author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Ads revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\advert\Entity\AdsInterface $entity
   *   The Ads entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(AdsInterface $entity);

  /**
   * Unsets the language for all Ads with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
