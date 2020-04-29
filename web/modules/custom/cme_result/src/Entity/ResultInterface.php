<?php

namespace Drupal\cme_result\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Result entities.
 *
 * @ingroup cme_result
 */
interface ResultInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Result name.
   *
   * @return string
   *   Name of the Result.
   */
  public function getName();

  /**
   * Sets the Result name.
   *
   * @param string $name
   *   The Result name.
   *
   * @return \Drupal\cme_result\Entity\ResultInterface
   *   The called Result entity.
   */
  public function setName($name);

  /**
   * Gets the Result creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Result.
   */
  public function getCreatedTime();

  /**
   * Sets the Result creation timestamp.
   *
   * @param int $timestamp
   *   The Result creation timestamp.
   *
   * @return \Drupal\cme_result\Entity\ResultInterface
   *   The called Result entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Result revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Result revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\cme_result\Entity\ResultInterface
   *   The called Result entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Result revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Result revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\cme_result\Entity\ResultInterface
   *   The called Result entity.
   */
  public function setRevisionUserId($uid);

}
