<?php

namespace Drupal\banner;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\banner\Entity\BannerEntityInterface;

/**
 * Defines the storage handler class for Banner entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Banner entity entities.
 *
 * @ingroup banner
 */
class BannerEntityStorage extends SqlContentEntityStorage implements BannerEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(BannerEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {banner_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {banner_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(BannerEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {banner_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('banner_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
