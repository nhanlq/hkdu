<?php

namespace Drupal\notify\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining User notify entities.
 *
 * @ingroup notify
 */
interface UserNotifyInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the User notify name.
   *
   * @return string
   *   Name of the User notify.
   */
  public function getName();

  /**
   * Sets the User notify name.
   *
   * @param string $name
   *   The User notify name.
   *
   * @return \Drupal\notify\Entity\UserNotifyInterface
   *   The called User notify entity.
   */
  public function setName($name);

  /**
   * Gets the User notify creation timestamp.
   *
   * @return int
   *   Creation timestamp of the User notify.
   */
  public function getCreatedTime();

  /**
   * Sets the User notify creation timestamp.
   *
   * @param int $timestamp
   *   The User notify creation timestamp.
   *
   * @return \Drupal\notify\Entity\UserNotifyInterface
   *   The called User notify entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the User notify revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the User notify revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\notify\Entity\UserNotifyInterface
   *   The called User notify entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the User notify revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the User notify revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\notify\Entity\UserNotifyInterface
   *   The called User notify entity.
   */
  public function setRevisionUserId($uid);

}
