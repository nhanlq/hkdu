<?php

namespace Drupal\healthy\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Healthy entities.
 *
 * @ingroup healthy
 */
interface HealthyInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Healthy name.
   *
   * @return string
   *   Name of the Healthy.
   */
  public function getName();

  /**
   * Sets the Healthy name.
   *
   * @param string $name
   *   The Healthy name.
   *
   * @return \Drupal\healthy\Entity\HealthyInterface
   *   The called Healthy entity.
   */
  public function setName($name);

  /**
   * Gets the Healthy creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Healthy.
   */
  public function getCreatedTime();

  /**
   * Sets the Healthy creation timestamp.
   *
   * @param int $timestamp
   *   The Healthy creation timestamp.
   *
   * @return \Drupal\healthy\Entity\HealthyInterface
   *   The called Healthy entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Healthy revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Healthy revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\healthy\Entity\HealthyInterface
   *   The called Healthy entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Healthy revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Healthy revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\healthy\Entity\HealthyInterface
   *   The called Healthy entity.
   */
  public function setRevisionUserId($uid);

}
