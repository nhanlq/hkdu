<?php

namespace Drupal\cme_event_organizer\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Event Organizer entities.
 *
 * @ingroup cme_event_organizer
 */
interface EventOrgInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Event Organizer name.
   *
   * @return string
   *   Name of the Event Organizer.
   */
  public function getName();

  /**
   * Sets the Event Organizer name.
   *
   * @param string $name
   *   The Event Organizer name.
   *
   * @return \Drupal\cme_event_organizer\Entity\EventOrgInterface
   *   The called Event Organizer entity.
   */
  public function setName($name);

  /**
   * Gets the Event Organizer creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Event Organizer.
   */
  public function getCreatedTime();

  /**
   * Sets the Event Organizer creation timestamp.
   *
   * @param int $timestamp
   *   The Event Organizer creation timestamp.
   *
   * @return \Drupal\cme_event_organizer\Entity\EventOrgInterface
   *   The called Event Organizer entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Event Organizer revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Event Organizer revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\cme_event_organizer\Entity\EventOrgInterface
   *   The called Event Organizer entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Event Organizer revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Event Organizer revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\cme_event_organizer\Entity\EventOrgInterface
   *   The called Event Organizer entity.
   */
  public function setRevisionUserId($uid);

}
