<?php

namespace Drupal\drug_search;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Drug search entities.
 *
 * @ingroup drug_search
 */
class DrugSearchListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Drug search ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\drug_search\Entity\DrugSearch $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.drug_search.edit_form',
      ['drug_search' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
