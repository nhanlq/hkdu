<?php

namespace Drupal\download\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Download entities.
 *
 * @ingroup download
 */
interface DownloadInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Download name.
   *
   * @return string
   *   Name of the Download.
   */
  public function getName();

  /**
   * Sets the Download name.
   *
   * @param string $name
   *   The Download name.
   *
   * @return \Drupal\download\Entity\DownloadInterface
   *   The called Download entity.
   */
  public function setName($name);

  /**
   * Gets the Download creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Download.
   */
  public function getCreatedTime();

  /**
   * Sets the Download creation timestamp.
   *
   * @param int $timestamp
   *   The Download creation timestamp.
   *
   * @return \Drupal\download\Entity\DownloadInterface
   *   The called Download entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Download revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Download revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\download\Entity\DownloadInterface
   *   The called Download entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Download revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Download revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\download\Entity\DownloadInterface
   *   The called Download entity.
   */
  public function setRevisionUserId($uid);

}
