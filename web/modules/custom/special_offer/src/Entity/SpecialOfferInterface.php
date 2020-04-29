<?php

namespace Drupal\special_offer\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Special offer entities.
 *
 * @ingroup special_offer
 */
interface SpecialOfferInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Special offer name.
   *
   * @return string
   *   Name of the Special offer.
   */
  public function getName();

  /**
   * Sets the Special offer name.
   *
   * @param string $name
   *   The Special offer name.
   *
   * @return \Drupal\special_offer\Entity\SpecialOfferInterface
   *   The called Special offer entity.
   */
  public function setName($name);

  /**
   * Gets the Special offer creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Special offer.
   */
  public function getCreatedTime();

  /**
   * Sets the Special offer creation timestamp.
   *
   * @param int $timestamp
   *   The Special offer creation timestamp.
   *
   * @return \Drupal\special_offer\Entity\SpecialOfferInterface
   *   The called Special offer entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Special offer revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Special offer revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\special_offer\Entity\SpecialOfferInterface
   *   The called Special offer entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Special offer revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Special offer revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\special_offer\Entity\SpecialOfferInterface
   *   The called Special offer entity.
   */
  public function setRevisionUserId($uid);

}
