<?php

namespace Drupal\external_link\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining External link entities.
 *
 * @ingroup external_link
 */
interface ExternalLinkInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the External link name.
   *
   * @return string
   *   Name of the External link.
   */
  public function getName();

  /**
   * Sets the External link name.
   *
   * @param string $name
   *   The External link name.
   *
   * @return \Drupal\external_link\Entity\ExternalLinkInterface
   *   The called External link entity.
   */
  public function setName($name);

  /**
   * Gets the External link creation timestamp.
   *
   * @return int
   *   Creation timestamp of the External link.
   */
  public function getCreatedTime();

  /**
   * Sets the External link creation timestamp.
   *
   * @param int $timestamp
   *   The External link creation timestamp.
   *
   * @return \Drupal\external_link\Entity\ExternalLinkInterface
   *   The called External link entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the External link revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the External link revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\external_link\Entity\ExternalLinkInterface
   *   The called External link entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the External link revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the External link revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\external_link\Entity\ExternalLinkInterface
   *   The called External link entity.
   */
  public function setRevisionUserId($uid);

}
