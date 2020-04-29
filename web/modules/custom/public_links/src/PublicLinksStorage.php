<?php

namespace Drupal\public_links;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\public_links\Entity\PublicLinksInterface;

/**
 * Defines the storage handler class for Public links entities.
 *
 * This extends the base storage class, adding required special handling for
 * Public links entities.
 *
 * @ingroup public_links
 */
class PublicLinksStorage extends SqlContentEntityStorage implements PublicLinksStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(PublicLinksInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {public_links_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {public_links_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(PublicLinksInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {public_links_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('public_links_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
