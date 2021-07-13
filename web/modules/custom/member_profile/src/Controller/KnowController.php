<?php

namespace Drupal\member_profile\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class Committee Controller.
 */
class KnowController extends ControllerBase {

  /**
   * Article.
   *
   * @return array
   *   Return News.
   */
  public function index() {

    return [
      'abouts' => [
        '#theme' => ['member_drug'],
        '#article' => $this->getSharing(),
        '#name' => $_GET['name'],
        '#company' => $_GET['company'],
      ],

      'pager' => [
        '#type' => 'pager',
      ],
    ];
  }

  public function getSharing() {
    $ids1 = [];
    $ids2 = [];
    $ids3 = [];
    $ids4 = [];
    if (isset($_GET['name']) && !empty($_GET['name'])) {
      $ids1 = \Drupal::entityQuery('node')
        ->condition('type', 'know')
        ->condition('status', 1)
        ->condition('title', $_GET['name'], 'CONTAINS')
        ->sort('field_common_name', 'ASC')
        ->execute();
      $ids2 = \Drupal::entityQuery('node')
        ->condition('type', 'know')
        ->condition('status', 1)
        ->condition('field_common_name', $_GET['name'], 'CONTAINS')
        ->sort('field_common_name', 'ASC')
        ->execute();

    }
    if (isset($_GET['company']) && !empty($_GET['company'])) {
      $ids3 = \Drupal::entityQuery('node')
        ->condition('type', 'know')
        ->condition('status', 1)
        ->condition('field_company', $_GET['company'], 'CONTAINS')
        ->sort('field_common_name', 'ASC')
        ->execute();
    }
    if (empty($_GET['name']) && empty($_GET['company'])) {
      $ids4 = \Drupal::entityQuery('node')
        ->condition('type', 'know')
        ->condition('status', 1)
        ->sort('field_common_name', 'ASC')
        ->execute();
    }
    $ids = array_merge($ids1, $ids2, $ids3, $ids4);
    $result = \Drupal\node\Entity\Node::loadMultiple($ids);
    return $result;
  }

}
