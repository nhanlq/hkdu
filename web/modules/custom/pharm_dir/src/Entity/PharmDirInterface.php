<?php

namespace Drupal\pharm_dir\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Pharm dir entities.
 *
 * @ingroup pharm_dir
 */
interface PharmDirInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Pharm dir name.
   *
   * @return string
   *   Name of the Pharm dir.
   */
  public function getName();

  /**
   * Sets the Pharm dir name.
   *
   * @param string $name
   *   The Pharm dir name.
   *
   * @return \Drupal\pharm_dir\Entity\PharmDirInterface
   *   The called Pharm dir entity.
   */
  public function setName($name);

  /**
   * Gets the Pharm dir creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Pharm dir.
   */
  public function getCreatedTime();

  /**
   * Sets the Pharm dir creation timestamp.
   *
   * @param int $timestamp
   *   The Pharm dir creation timestamp.
   *
   * @return \Drupal\pharm_dir\Entity\PharmDirInterface
   *   The called Pharm dir entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Pharm dir revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Pharm dir revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\pharm_dir\Entity\PharmDirInterface
   *   The called Pharm dir entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Pharm dir revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Pharm dir revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\pharm_dir\Entity\PharmDirInterface
   *   The called Pharm dir entity.
   */
  public function setRevisionUserId($uid);

}
