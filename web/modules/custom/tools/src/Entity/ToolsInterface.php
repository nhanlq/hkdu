<?php

namespace Drupal\tools\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Tools entities.
 *
 * @ingroup tools
 */
interface ToolsInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Tools name.
   *
   * @return string
   *   Name of the Tools.
   */
  public function getName();

  /**
   * Sets the Tools name.
   *
   * @param string $name
   *   The Tools name.
   *
   * @return \Drupal\tools\Entity\ToolsInterface
   *   The called Tools entity.
   */
  public function setName($name);

  /**
   * Gets the Tools creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Tools.
   */
  public function getCreatedTime();

  /**
   * Sets the Tools creation timestamp.
   *
   * @param int $timestamp
   *   The Tools creation timestamp.
   *
   * @return \Drupal\tools\Entity\ToolsInterface
   *   The called Tools entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Tools revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Tools revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\tools\Entity\ToolsInterface
   *   The called Tools entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Tools revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Tools revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\tools\Entity\ToolsInterface
   *   The called Tools entity.
   */
  public function setRevisionUserId($uid);

}
