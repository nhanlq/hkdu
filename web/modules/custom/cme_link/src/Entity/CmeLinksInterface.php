<?php

namespace Drupal\cme_link\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining CME Links entities.
 *
 * @ingroup cme_link
 */
interface CmeLinksInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the CME Links name.
   *
   * @return string
   *   Name of the CME Links.
   */
  public function getName();

  /**
   * Sets the CME Links name.
   *
   * @param string $name
   *   The CME Links name.
   *
   * @return \Drupal\cme_link\Entity\CmeLinksInterface
   *   The called CME Links entity.
   */
  public function setName($name);

  /**
   * Gets the CME Links creation timestamp.
   *
   * @return int
   *   Creation timestamp of the CME Links.
   */
  public function getCreatedTime();

  /**
   * Sets the CME Links creation timestamp.
   *
   * @param int $timestamp
   *   The CME Links creation timestamp.
   *
   * @return \Drupal\cme_link\Entity\CmeLinksInterface
   *   The called CME Links entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the CME Links revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the CME Links revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\cme_link\Entity\CmeLinksInterface
   *   The called CME Links entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the CME Links revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the CME Links revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\cme_link\Entity\CmeLinksInterface
   *   The called CME Links entity.
   */
  public function setRevisionUserId($uid);

}
