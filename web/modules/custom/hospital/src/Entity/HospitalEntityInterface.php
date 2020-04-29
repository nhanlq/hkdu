<?php

namespace Drupal\hospital\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Hospital entity entities.
 *
 * @ingroup hospital
 */
interface HospitalEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Hospital entity name.
   *
   * @return string
   *   Name of the Hospital entity.
   */
  public function getName();

  /**
   * Sets the Hospital entity name.
   *
   * @param string $name
   *   The Hospital entity name.
   *
   * @return \Drupal\hospital\Entity\HospitalEntityInterface
   *   The called Hospital entity entity.
   */
  public function setName($name);

  /**
   * Gets the Hospital entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Hospital entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Hospital entity creation timestamp.
   *
   * @param int $timestamp
   *   The Hospital entity creation timestamp.
   *
   * @return \Drupal\hospital\Entity\HospitalEntityInterface
   *   The called Hospital entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Hospital entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Hospital entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\hospital\Entity\HospitalEntityInterface
   *   The called Hospital entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Hospital entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Hospital entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\hospital\Entity\HospitalEntityInterface
   *   The called Hospital entity entity.
   */
  public function setRevisionUserId($uid);

}
