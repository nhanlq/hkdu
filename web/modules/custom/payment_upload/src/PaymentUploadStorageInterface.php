<?php

namespace Drupal\payment_upload;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\payment_upload\Entity\PaymentUploadInterface;

/**
 * Defines the storage handler class for Payment upload entities.
 *
 * This extends the base storage class, adding required special handling for
 * Payment upload entities.
 *
 * @ingroup payment_upload
 */
interface PaymentUploadStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Payment upload revision IDs for a specific Payment upload.
   *
   * @param \Drupal\payment_upload\Entity\PaymentUploadInterface $entity
   *   The Payment upload entity.
   *
   * @return int[]
   *   Payment upload revision IDs (in ascending order).
   */
  public function revisionIds(PaymentUploadInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Payment upload author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Payment upload revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\payment_upload\Entity\PaymentUploadInterface $entity
   *   The Payment upload entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(PaymentUploadInterface $entity);

  /**
   * Unsets the language for all Payment upload with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
