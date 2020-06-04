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

        $name = NULL;
        if (isset($_GET['name'])) {
            $name = $_GET['name'];
        }
        return [
            'results' => [
                '#theme' => 'doctor_list',
                '#doctors' => $this->getDoctors(),
                '#name' => $name
            ],
            'pager' => [
                '#type' => 'pager',
            ],
        ];
    }

    public function getDoctors()
    {
        if (isset($_GET['name']) && !empty($_GET['name'])) {
            $ids = \Drupal::entityQuery('doctor')
                ->condition('status', 1)
                ->condition('name', $_GET['name'])
                ->sort('field_weight','ASC')
                ->sort('created','DESC')
                ->pager(15)
                ->execute();
        }else{
            $ids = \Drupal::entityQuery('doctor')
                ->condition('status', 1)
                ->sort('field_weight','ASC')
                ->sort('created','DESC')
                ->pager(15)
                ->execute();
        }

        $result = DoctorEntity::loadMultiple($ids);
        return $result;
    }
    public function title(){
        return ['#markup' => \Drupal::state()->get('/doctors','Doctors'), '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList()];
    }

}
