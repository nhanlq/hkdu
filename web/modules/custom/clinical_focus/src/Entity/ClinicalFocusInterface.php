<?php

namespace Drupal\clinical_focus\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Clinical focus entities.
 *
 * @ingroup clinical_focus
 */
interface ClinicalFocusInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Clinical focus name.
   *
   * @return string
   *   Name of the Clinical focus.
   */
  public function getName();

  /**
   * Sets the Clinical focus name.
   *
   * @param string $name
   *   The Clinical focus name.
   *
   * @return \Drupal\clinical_focus\Entity\ClinicalFocusInterface
   *   The called Clinical focus entity.
   */
  public function setName($name);

  /**
   * Gets the Clinical focus creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Clinical focus.
   */
  public function getCreatedTime();

  /**
   * Sets the Clinical focus creation timestamp.
   *
   * @param int $timestamp
   *   The Clinical focus creation timestamp.
   *
   * @return \Drupal\clinical_focus\Entity\ClinicalFocusInterface
   *   The called Clinical focus entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Clinical focus revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Clinical focus revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\clinical_focus\Entity\ClinicalFocusInterface
   *   The called Clinical focus entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Clinical focus revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Clinical focus revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\clinical_focus\Entity\ClinicalFocusInterface
   *   The called Clinical focus entity.
   */
  public function setRevisionUserId($uid);

}
