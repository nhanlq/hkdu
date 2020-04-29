<?php

namespace Drupal\doctor\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Doctor entity entities.
 *
 * @ingroup doctor
 */
interface DoctorEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Doctor entity name.
   *
   * @return string
   *   Name of the Doctor entity.
   */
  public function getName();

  /**
   * Sets the Doctor entity name.
   *
   * @param string $name
   *   The Doctor entity name.
   *
   * @return \Drupal\doctor\Entity\DoctorEntityInterface
   *   The called Doctor entity entity.
   */
  public function setName($name);

  /**
   * Gets the Doctor entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Doctor entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Doctor entity creation timestamp.
   *
   * @param int $timestamp
   *   The Doctor entity creation timestamp.
   *
   * @return \Drupal\doctor\Entity\DoctorEntityInterface
   *   The called Doctor entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Doctor entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Doctor entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\doctor\Entity\DoctorEntityInterface
   *   The called Doctor entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Doctor entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Doctor entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\doctor\Entity\DoctorEntityInterface
   *   The called Doctor entity entity.
   */
  public function setRevisionUserId($uid);

}
