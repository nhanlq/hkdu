<?php

namespace Drupal\public_links\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Public links entities.
 *
 * @ingroup public_links
 */
interface PublicLinksInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Public links name.
   *
   * @return string
   *   Name of the Public links.
   */
  public function getName();

  /**
   * Sets the Public links name.
   *
   * @param string $name
   *   The Public links name.
   *
   * @return \Drupal\public_links\Entity\PublicLinksInterface
   *   The called Public links entity.
   */
  public function setName($name);

  /**
   * Gets the Public links creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Public links.
   */
  public function getCreatedTime();

  /**
   * Sets the Public links creation timestamp.
   *
   * @param int $timestamp
   *   The Public links creation timestamp.
   *
   * @return \Drupal\public_links\Entity\PublicLinksInterface
   *   The called Public links entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Public links revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Public links revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\public_links\Entity\PublicLinksInterface
   *   The called Public links entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Public links revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Public links revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\public_links\Entity\PublicLinksInterface
   *   The called Public links entity.
   */
  public function setRevisionUserId($uid);

}
