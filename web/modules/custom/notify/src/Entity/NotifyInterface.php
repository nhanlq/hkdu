<?php

namespace Drupal\notify\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Notify entities.
 *
 * @ingroup notify
 */
interface NotifyInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Notify name.
   *
   * @return string
   *   Name of the Notify.
   */
  public function getName();

  /**
   * Sets the Notify name.
   *
   * @param string $name
   *   The Notify name.
   *
   * @return \Drupal\notify\Entity\NotifyInterface
   *   The called Notify entity.
   */
  public function setName($name);

  /**
   * Gets the Notify creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Notify.
   */
  public function getCreatedTime();

  /**
   * Sets the Notify creation timestamp.
   *
   * @param int $timestamp
   *   The Notify creation timestamp.
   *
   * @return \Drupal\notify\Entity\NotifyInterface
   *   The called Notify entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Notify revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Notify revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\notify\Entity\NotifyInterface
   *   The called Notify entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Notify revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Notify revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\notify\Entity\NotifyInterface
   *   The called Notify entity.
   */
  public function setRevisionUserId($uid);

}
