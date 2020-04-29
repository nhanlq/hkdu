<?php

namespace Drupal\about\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Default entity entities.
 *
 * @ingroup about
 */
interface DefaultEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Default entity name.
   *
   * @return string
   *   Name of the Default entity.
   */
  public function getName();

  /**
   * Sets the Default entity name.
   *
   * @param string $name
   *   The Default entity name.
   *
   * @return \Drupal\about\Entity\DefaultEntityInterface
   *   The called Default entity entity.
   */
  public function setName($name);

  /**
   * Gets the Default entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Default entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Default entity creation timestamp.
   *
   * @param int $timestamp
   *   The Default entity creation timestamp.
   *
   * @return \Drupal\about\Entity\DefaultEntityInterface
   *   The called Default entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Default entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Default entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\about\Entity\DefaultEntityInterface
   *   The called Default entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Default entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Default entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\about\Entity\DefaultEntityInterface
   *   The called Default entity entity.
   */
  public function setRevisionUserId($uid);

}
