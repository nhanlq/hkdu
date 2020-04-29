<?php

namespace Drupal\quiz_tracking\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Quiz tracking entities.
 *
 * @ingroup quiz_tracking
 */
interface QuizTrackingInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Quiz tracking name.
   *
   * @return string
   *   Name of the Quiz tracking.
   */
  public function getName();

  /**
   * Sets the Quiz tracking name.
   *
   * @param string $name
   *   The Quiz tracking name.
   *
   * @return \Drupal\quiz_tracking\Entity\QuizTrackingInterface
   *   The called Quiz tracking entity.
   */
  public function setName($name);

  /**
   * Gets the Quiz tracking creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Quiz tracking.
   */
  public function getCreatedTime();

  /**
   * Sets the Quiz tracking creation timestamp.
   *
   * @param int $timestamp
   *   The Quiz tracking creation timestamp.
   *
   * @return \Drupal\quiz_tracking\Entity\QuizTrackingInterface
   *   The called Quiz tracking entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Quiz tracking revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Quiz tracking revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\quiz_tracking\Entity\QuizTrackingInterface
   *   The called Quiz tracking entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Quiz tracking revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Quiz tracking revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\quiz_tracking\Entity\QuizTrackingInterface
   *   The called Quiz tracking entity.
   */
  public function setRevisionUserId($uid);

}
