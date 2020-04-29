<?php

namespace Drupal\special_offer;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\special_offer\Entity\SpecialOfferInterface;

/**
 * Defines the storage handler class for Special offer entities.
 *
 * This extends the base storage class, adding required special handling for
 * Special offer entities.
 *
 * @ingroup special_offer
 */
interface SpecialOfferStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Special offer revision IDs for a specific Special offer.
   *
   * @param \Drupal\special_offer\Entity\SpecialOfferInterface $entity
   *   The Special offer entity.
   *
   * @return int[]
   *   Special offer revision IDs (in ascending order).
   */
  public function revisionIds(SpecialOfferInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Special offer author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Special offer revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\special_offer\Entity\SpecialOfferInterface $entity
   *   The Special offer entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(SpecialOfferInterface $entity);

  /**
   * Unsets the language for all Special offer with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
