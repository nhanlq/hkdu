<?php

namespace Drupal\advert;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class AdsStorage extends SqlContentEntityStorage implements AdsStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(AdsInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {ads_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {ads_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(AdsInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {ads_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('ads_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
