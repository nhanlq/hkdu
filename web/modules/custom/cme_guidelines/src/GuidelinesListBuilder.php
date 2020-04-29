<?php

namespace Drupal\cme_guidelines;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Guidelines entities.
 *
 * @ingroup cme_guidelines
 */
class GuidelinesListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Guidelines ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\cme_guidelines\Entity\Guidelines $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.guidelines.edit_form',
      ['guidelines' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
