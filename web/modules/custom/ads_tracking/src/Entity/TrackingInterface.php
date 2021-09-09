<?php

namespace Drupal\ads_tracking\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Tracking entities.
 *
 * @ingroup ads_tracking
 */
interface TrackingInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Tracking name.
   *
   * @return string
   *   Name of the Tracking.
   */
  public function getName();

  /**
   * Sets the Tracking name.
   *
   * @param string $name
   *   The Tracking name.
   *
   * @return \Drupal\ads_tracking\Entity\TrackingInterface
   *   The called Tracking entity.
   */
  public function setName($name);

  /**
   * Gets the Tracking creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Tracking.
   */
  public function getCreatedTime();

  /**
   * Sets the Tracking creation timestamp.
   *
   * @param int $timestamp
   *   The Tracking creation timestamp.
   *
   * @return \Drupal\ads_tracking\Entity\TrackingInterface
   *   The called Tracking entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Tracking revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Tracking revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\ads_tracking\Entity\TrackingInterface
   *   The called Tracking entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Tracking revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Tracking revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\ads_tracking\Entity\TrackingInterface
   *   The called Tracking entity.
   */
  public function setRevisionUserId($uid);

}
