<?php

namespace Drupal\hospital\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\hospital\Entity\HospitalEntity;

/**
 * Class HospitalListController.
 */
class HospitalListController extends ControllerBase {

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
                '#theme' => 'hospital_list',
                '#hospitals' => $this->getHospitals(),
            ],
            'pager' => [
                '#type' => 'pager',
            ],
        ];
    }

    public function getHospitals()
    {
        $ids = \Drupal::entityQuery('doctor')
            ->condition('status', 1)
            ->sort('field_weight','ASC')
            ->sort('created','DESC')
            ->pager(15)
            ->execute();
        $result = HospitalEntity::loadMultiple($ids);
        return $result;
    }

}
