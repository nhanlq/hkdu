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
            '#theme' => array('cme_guidelines_list'),
            '#abouts' => $this->getAllAbout(),
        );
    }

    public function getAllAbout(){
        $ids = \Drupal::entityQuery('guidelines')
            ->condition('status', 1)
            ->execute();
        $result = \Drupal\cme_guidelines\Entity\Guidelines::loadMultiple($ids);
        return $result;
    }

}
