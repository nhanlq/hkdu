<?php

namespace Drupal\banner\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Banner entity entities.
 *
 * @ingroup banner
 */
interface BannerEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Banner entity name.
   *
   * @return string
   *   Name of the Banner entity.
   */
  public function getName();

  /**
   * Sets the Banner entity name.
   *
   * @param string $name
   *   The Banner entity name.
   *
   * @return \Drupal\banner\Entity\BannerEntityInterface
   *   The called Banner entity entity.
   */
  public function setName($name);

  /**
   * Gets the Banner entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Banner entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Banner entity creation timestamp.
   *
   * @param int $timestamp
   *   The Banner entity creation timestamp.
   *
   * @return \Drupal\banner\Entity\BannerEntityInterface
   *   The called Banner entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Banner entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Banner entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\banner\Entity\BannerEntityInterface
   *   The called Banner entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Banner entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Banner entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\banner\Entity\BannerEntityInterface
   *   The called Banner entity entity.
   */
  public function setRevisionUserId($uid);

}
