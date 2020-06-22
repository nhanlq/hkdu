<?php

namespace Drupal\cme_guidelines\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class GuideListController.
 */
class GuideListController extends ControllerBase {

  /**
   * List.
   *
   * @return string
   *   Return Hello string.
   */
    public function list() {
        return array(
            'results'=>[
            '#theme' => array('cme_guidelines_list'),
            '#abouts' => $this->getAllAbout(),
                ],
            'pager' => [
                '#type' => 'pager',
            ],
        );
    }

    public function getAllAbout(){
        $ids = \Drupal::entityQuery('guidelines')
            ->condition('status', 1)
            ->sort('field_weight','ASC')
            ->sort('field_publish_date','DESC')
            ->pager(10)
            ->execute();
        $result = \Drupal\cme_guidelines\Entity\Guidelines::loadMultiple($ids);
        return $result;
    }
    public function title(){
        return ['#markup' => \Drupal::state()->get('/cme/guidelines','Guidelines'), '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList()];
    }

}
