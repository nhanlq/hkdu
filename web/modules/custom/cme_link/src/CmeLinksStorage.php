<?php

namespace Drupal\cme_link;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\cme_link\Entity\CmeLinksInterface;

/**
 * Defines the storage handler class for CME Links entities.
 *
 * This extends the base storage class, adding required special handling for
 * CME Links entities.
 *
 * @ingroup cme_link
 */
class CmeLinksStorage extends SqlContentEntityStorage implements CmeLinksStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(CmeLinksInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {cme_links_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {cme_links_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(CmeLinksInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {cme_links_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('cme_links_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
