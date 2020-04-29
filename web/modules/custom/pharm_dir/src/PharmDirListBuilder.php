<?php

namespace Drupal\pharm_dir;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Pharm dir entities.
 *
 * @ingroup pharm_dir
 */
class PharmDirListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Pharm dir ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\pharm_dir\Entity\PharmDir $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.pharm_dir.edit_form',
      ['pharm_dir' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
