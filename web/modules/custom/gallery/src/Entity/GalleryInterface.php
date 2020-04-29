<?php

namespace Drupal\gallery\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Gallery entities.
 *
 * @ingroup gallery
 */
interface GalleryInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Gallery name.
   *
   * @return string
   *   Name of the Gallery.
   */
  public function getName();

  /**
   * Sets the Gallery name.
   *
   * @param string $name
   *   The Gallery name.
   *
   * @return \Drupal\gallery\Entity\GalleryInterface
   *   The called Gallery entity.
   */
  public function setName($name);

  /**
   * Gets the Gallery creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Gallery.
   */
  public function getCreatedTime();

  /**
   * Sets the Gallery creation timestamp.
   *
   * @param int $timestamp
   *   The Gallery creation timestamp.
   *
   * @return \Drupal\gallery\Entity\GalleryInterface
   *   The called Gallery entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Gallery revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Gallery revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\gallery\Entity\GalleryInterface
   *   The called Gallery entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Gallery revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Gallery revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\gallery\Entity\GalleryInterface
   *   The called Gallery entity.
   */
  public function setRevisionUserId($uid);

}
