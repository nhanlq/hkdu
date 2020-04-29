<?php

namespace Drupal\drug_news\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Drug news entities.
 *
 * @ingroup drug_news
 */
interface DrugNewsInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Drug news name.
   *
   * @return string
   *   Name of the Drug news.
   */
  public function getName();

  /**
   * Sets the Drug news name.
   *
   * @param string $name
   *   The Drug news name.
   *
   * @return \Drupal\drug_news\Entity\DrugNewsInterface
   *   The called Drug news entity.
   */
  public function setName($name);

  /**
   * Gets the Drug news creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Drug news.
   */
  public function getCreatedTime();

  /**
   * Sets the Drug news creation timestamp.
   *
   * @param int $timestamp
   *   The Drug news creation timestamp.
   *
   * @return \Drupal\drug_news\Entity\DrugNewsInterface
   *   The called Drug news entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Drug news revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Drug news revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\drug_news\Entity\DrugNewsInterface
   *   The called Drug news entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Drug news revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Drug news revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\drug_news\Entity\DrugNewsInterface
   *   The called Drug news entity.
   */
  public function setRevisionUserId($uid);

}
