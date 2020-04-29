<?php

namespace Drupal\media_release\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Media entity entities.
 *
 * @ingroup media_release
 */
interface MediaEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Media entity name.
   *
   * @return string
   *   Name of the Media entity.
   */
  public function getName();

  /**
   * Sets the Media entity name.
   *
   * @param string $name
   *   The Media entity name.
   *
   * @return \Drupal\media_release\Entity\MediaEntityInterface
   *   The called Media entity entity.
   */
  public function setName($name);

  /**
   * Gets the Media entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Media entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Media entity creation timestamp.
   *
   * @param int $timestamp
   *   The Media entity creation timestamp.
   *
   * @return \Drupal\media_release\Entity\MediaEntityInterface
   *   The called Media entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Media entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Media entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\media_release\Entity\MediaEntityInterface
   *   The called Media entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Media entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Media entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\media_release\Entity\MediaEntityInterface
   *   The called Media entity entity.
   */
  public function setRevisionUserId($uid);

}
