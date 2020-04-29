<?php

namespace Drupal\quiz_tracking;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Quiz tracking entities.
 *
 * @ingroup quiz_tracking
 */
class QuizTrackingListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Quiz tracking ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\quiz_tracking\Entity\QuizTracking $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.quiz_tracking.edit_form',
      ['quiz_tracking' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
