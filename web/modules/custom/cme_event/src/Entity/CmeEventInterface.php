<?php

namespace Drupal\cme_event\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining CME Event entities.
 *
 * @ingroup cme_event
 */
interface CmeEventInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the CME Event name.
   *
   * @return string
   *   Name of the CME Event.
   */
  public function getName();

  /**
   * Sets the CME Event name.
   *
   * @param string $name
   *   The CME Event name.
   *
   * @return \Drupal\cme_event\Entity\CmeEventInterface
   *   The called CME Event entity.
   */
  public function setName($name);

  /**
   * Gets the CME Event creation timestamp.
   *
   * @return int
   *   Creation timestamp of the CME Event.
   */
  public function getCreatedTime();

  /**
   * Sets the CME Event creation timestamp.
   *
   * @param int $timestamp
   *   The CME Event creation timestamp.
   *
   * @return \Drupal\cme_event\Entity\CmeEventInterface
   *   The called CME Event entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the CME Event revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the CME Event revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\cme_event\Entity\CmeEventInterface
   *   The called CME Event entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the CME Event revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the CME Event revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\cme_event\Entity\CmeEventInterface
   *   The called CME Event entity.
   */
  public function setRevisionUserId($uid);

}
