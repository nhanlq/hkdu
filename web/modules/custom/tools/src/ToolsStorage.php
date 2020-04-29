<?php

namespace Drupal\tools;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\tools\Entity\ToolsInterface;

/**
 * Defines the storage handler class for Tools entities.
 *
 * This extends the base storage class, adding required special handling for
 * Tools entities.
 *
 * @ingroup tools
 */
class ToolsStorage extends SqlContentEntityStorage implements ToolsStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(ToolsInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {tools_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {tools_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(ToolsInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {tools_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('tools_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
