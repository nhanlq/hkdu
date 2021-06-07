<?php

namespace Drupal\user_point\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Point entities.
 *
 * @ingroup user_point
 */
interface PointInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Point name.
   *
   * @return string
   *   Name of the Point.
   */
  public function getName();

  /**
   * Sets the Point name.
   *
   * @param string $name
   *   The Point name.
   *
   * @return \Drupal\user_point\Entity\PointInterface
   *   The called Point entity.
   */
  public function setName($name);

  /**
   * Gets the Point creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Point.
   */
  public function getCreatedTime();

  /**
   * Sets the Point creation timestamp.
   *
   * @param int $timestamp
   *   The Point creation timestamp.
   *
   * @return \Drupal\user_point\Entity\PointInterface
   *   The called Point entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Point revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Point revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\user_point\Entity\PointInterface
   *   The called Point entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Point revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Point revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\user_point\Entity\PointInterface
   *   The called Point entity.
   */
  public function setRevisionUserId($uid);

}
