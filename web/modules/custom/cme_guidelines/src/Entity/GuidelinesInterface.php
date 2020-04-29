<?php

namespace Drupal\cme_guidelines\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Guidelines entities.
 *
 * @ingroup cme_guidelines
 */
interface GuidelinesInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Guidelines name.
   *
   * @return string
   *   Name of the Guidelines.
   */
  public function getName();

  /**
   * Sets the Guidelines name.
   *
   * @param string $name
   *   The Guidelines name.
   *
   * @return \Drupal\cme_guidelines\Entity\GuidelinesInterface
   *   The called Guidelines entity.
   */
  public function setName($name);

  /**
   * Gets the Guidelines creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Guidelines.
   */
  public function getCreatedTime();

  /**
   * Sets the Guidelines creation timestamp.
   *
   * @param int $timestamp
   *   The Guidelines creation timestamp.
   *
   * @return \Drupal\cme_guidelines\Entity\GuidelinesInterface
   *   The called Guidelines entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Guidelines revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Guidelines revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\cme_guidelines\Entity\GuidelinesInterface
   *   The called Guidelines entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Guidelines revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Guidelines revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\cme_guidelines\Entity\GuidelinesInterface
   *   The called Guidelines entity.
   */
  public function setRevisionUserId($uid);

}
