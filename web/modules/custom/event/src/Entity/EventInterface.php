<?php

namespace Drupal\event\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Event entities.
 *
 * @ingroup event
 */
interface EventInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Event name.
   *
   * @return string
   *   Name of the Event.
   */
  public function getName();

  /**
   * Sets the Event name.
   *
   * @param string $name
   *   The Event name.
   *
   * @return \Drupal\event\Entity\EventInterface
   *   The called Event entity.
   */
  public function setName($name);

  /**
   * Gets the Event creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Event.
   */
  public function getCreatedTime();

  /**
   * Sets the Event creation timestamp.
   *
   * @param int $timestamp
   *   The Event creation timestamp.
   *
   * @return \Drupal\event\Entity\EventInterface
   *   The called Event entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Event revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Event revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\event\Entity\EventInterface
   *   The called Event entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Event revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Event revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\event\Entity\EventInterface
   *   The called Event entity.
   */
  public function setRevisionUserId($uid);

}
