<?php

namespace Drupal\special_offer;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class SpecialOfferStorage extends SqlContentEntityStorage implements SpecialOfferStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(SpecialOfferInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {special_offer_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {special_offer_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(SpecialOfferInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {special_offer_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('special_offer_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
