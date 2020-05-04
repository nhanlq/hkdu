<?php

namespace Drupal\doctor\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\doctor\Entity\DoctorEntity;

/**
 * Class DoctorListController.
 */
class DoctorListController extends ControllerBase {

  /**
   * List.
   *
   * @return string
   *   Return Hello string.
   */
    public function list()
    {

        return [
            'results' => [
                '#theme' => 'doctor_list',
                '#doctors' => $this->getDoctors(),
            ],
            'pager' => [
                '#type' => 'pager',
            ],
        ];
    }

    public function getDoctors()
    {
        $ids = \Drupal::entityQuery('doctor')
            ->condition('status', 1)
            ->sort('field_weight','ASC')
            ->sort('created','DESC')
            ->pager(15)
            ->execute();
        $result = DoctorEntity::loadMultiple($ids);
        return $result;
    }

}
