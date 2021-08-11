<?php

namespace Drupal\pharm_dir\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class PharmDirListController.
 */
class PharmDirListController extends ControllerBase {

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
                '#theme' => 'pharm_dir_list',
                '#pharm_dir' => $this->getHospitals(),
                '#get' => $_GET['key']
            ],
            'pager' => [
                '#type' => 'pager',
            ],
          '#cache' => [
            'max-age' => 0,
          ],
        ];
    }

    public function getHospitals()
    {
        $name = false;
        if(isset($_GET['key'])){
            $name = $_GET['key'];
        }
        if($name){
            $ids = \Drupal::entityQuery('pharm_dir')
                ->condition('status', 1)
                ->condition('name',$name,'CONTAINS')
                ->sort('field_weight','ASC')
                ->sort('field_publish_date','DESC')
                ->pager(12)
                ->execute();
        }else{
            $ids = \Drupal::entityQuery('pharm_dir')
                ->condition('status', 1)
                ->sort('field_weight','ASC')
                ->sort('field_publish_date','DESC')
                ->pager(12)
                ->execute();
        }

        $result = \Drupal\pharm_dir\Entity\PharmDir::loadMultiple($ids);
        return $result;
    }
    public function title(){
        return ['#markup' => \Drupal::state()->get('/e-pharm/pharm-dir','Pharm Dir'), '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList()];
    }

}
