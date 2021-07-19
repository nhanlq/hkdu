<?php

namespace Drupal\payment_upload;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class PaymentUploadStorage extends SqlContentEntityStorage implements PaymentUploadStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(PaymentUploadInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {payment_upload_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {payment_upload_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(PaymentUploadInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {payment_upload_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('payment_upload_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
