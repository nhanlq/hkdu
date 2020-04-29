<?php

namespace Drupal\drug_search\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Drug search entities.
 *
 * @ingroup drug_search
 */
interface DrugSearchInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Drug search name.
   *
   * @return string
   *   Name of the Drug search.
   */
  public function getName();

  /**
   * Sets the Drug search name.
   *
   * @param string $name
   *   The Drug search name.
   *
   * @return \Drupal\drug_search\Entity\DrugSearchInterface
   *   The called Drug search entity.
   */
  public function setName($name);

  /**
   * Gets the Drug search creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Drug search.
   */
  public function getCreatedTime();

  /**
   * Sets the Drug search creation timestamp.
   *
   * @param int $timestamp
   *   The Drug search creation timestamp.
   *
   * @return \Drupal\drug_search\Entity\DrugSearchInterface
   *   The called Drug search entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Drug search revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Drug search revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\drug_search\Entity\DrugSearchInterface
   *   The called Drug search entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Drug search revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Drug search revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\drug_search\Entity\DrugSearchInterface
   *   The called Drug search entity.
   */
  public function setRevisionUserId($uid);

}
