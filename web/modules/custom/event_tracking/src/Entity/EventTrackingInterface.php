<?php

namespace Drupal\event_tracking\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Event tracking entities.
 *
 * @ingroup event_tracking
 */
interface EventTrackingInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Event tracking name.
   *
   * @return string
   *   Name of the Event tracking.
   */
  public function getName();

  /**
   * Sets the Event tracking name.
   *
   * @param string $name
   *   The Event tracking name.
   *
   * @return \Drupal\event_tracking\Entity\EventTrackingInterface
   *   The called Event tracking entity.
   */
  public function setName($name);

  /**
   * Gets the Event tracking creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Event tracking.
   */
  public function getCreatedTime();

  /**
   * Sets the Event tracking creation timestamp.
   *
   * @param int $timestamp
   *   The Event tracking creation timestamp.
   *
   * @return \Drupal\event_tracking\Entity\EventTrackingInterface
   *   The called Event tracking entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Event tracking revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Event tracking revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\event_tracking\Entity\EventTrackingInterface
   *   The called Event tracking entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Event tracking revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Event tracking revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\event_tracking\Entity\EventTrackingInterface
   *   The called Event tracking entity.
   */
  public function setRevisionUserId($uid);

}
