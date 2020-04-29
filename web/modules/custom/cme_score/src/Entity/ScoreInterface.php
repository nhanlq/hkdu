<?php

namespace Drupal\cme_score\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Score entities.
 *
 * @ingroup cme_score
 */
interface ScoreInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Score name.
   *
   * @return string
   *   Name of the Score.
   */
  public function getName();

  /**
   * Sets the Score name.
   *
   * @param string $name
   *   The Score name.
   *
   * @return \Drupal\cme_score\Entity\ScoreInterface
   *   The called Score entity.
   */
  public function setName($name);

  /**
   * Gets the Score creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Score.
   */
  public function getCreatedTime();

  /**
   * Sets the Score creation timestamp.
   *
   * @param int $timestamp
   *   The Score creation timestamp.
   *
   * @return \Drupal\cme_score\Entity\ScoreInterface
   *   The called Score entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Score revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Score revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\cme_score\Entity\ScoreInterface
   *   The called Score entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Score revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Score revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\cme_score\Entity\ScoreInterface
   *   The called Score entity.
   */
  public function setRevisionUserId($uid);

}
