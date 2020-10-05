<?php

namespace Drupal\advert\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Ads entities.
 *
 * @ingroup advert
 */
interface AdsInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Ads name.
   *
   * @return string
   *   Name of the Ads.
   */
  public function getName();

  /**
   * Sets the Ads name.
   *
   * @param string $name
   *   The Ads name.
   *
   * @return \Drupal\advert\Entity\AdsInterface
   *   The called Ads entity.
   */
  public function setName($name);

  /**
   * Gets the Ads creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Ads.
   */
  public function getCreatedTime();

  /**
   * Sets the Ads creation timestamp.
   *
   * @param int $timestamp
   *   The Ads creation timestamp.
   *
   * @return \Drupal\advert\Entity\AdsInterface
   *   The called Ads entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Ads revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Ads revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\advert\Entity\AdsInterface
   *   The called Ads entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Ads revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Ads revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\advert\Entity\AdsInterface
   *   The called Ads entity.
   */
  public function setRevisionUserId($uid);

}
