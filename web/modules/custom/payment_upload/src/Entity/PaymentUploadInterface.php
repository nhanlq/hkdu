<?php

namespace Drupal\payment_upload\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Payment upload entities.
 *
 * @ingroup payment_upload
 */
interface PaymentUploadInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Payment upload name.
   *
   * @return string
   *   Name of the Payment upload.
   */
  public function getName();

  /**
   * Sets the Payment upload name.
   *
   * @param string $name
   *   The Payment upload name.
   *
   * @return \Drupal\payment_upload\Entity\PaymentUploadInterface
   *   The called Payment upload entity.
   */
  public function setName($name);

  /**
   * Gets the Payment upload creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Payment upload.
   */
  public function getCreatedTime();

  /**
   * Sets the Payment upload creation timestamp.
   *
   * @param int $timestamp
   *   The Payment upload creation timestamp.
   *
   * @return \Drupal\payment_upload\Entity\PaymentUploadInterface
   *   The called Payment upload entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Payment upload revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Payment upload revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\payment_upload\Entity\PaymentUploadInterface
   *   The called Payment upload entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Payment upload revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Payment upload revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\payment_upload\Entity\PaymentUploadInterface
   *   The called Payment upload entity.
   */
  public function setRevisionUserId($uid);

}
