<?php

namespace Drupal\clinical_focus;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Clinical focus entities.
 *
 * @ingroup clinical_focus
 */
class ClinicalFocusListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Clinical focus ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\clinical_focus\Entity\ClinicalFocus $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.clinical_focus.edit_form',
      ['clinical_focus' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
