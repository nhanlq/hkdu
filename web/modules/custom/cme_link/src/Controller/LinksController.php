<?php

namespace Drupal\cme_link\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class LinksController.
 */
class LinksController extends ControllerBase {

  /**
   * List.
   *
   * @return string
   *   Return Hello string.
   */
    public function list() {
        return [
            'results' => [
                '#theme' => 'cme_link_list',
                '#external_link' => $this->getExternalLink(),
            ],
            'pager' => [
                '#type' => 'pager',
            ],
        ];
    }

    public function getExternalLink()
    {

        $ids = \Drupal::entityQuery('cme_links')
            ->condition('status', 1)
            ->sort('field_weight','ASC')
            ->pager(10)
            ->execute();
        $result = \Drupal\cme_link\Entity\CmeLinks::loadMultiple($ids);
        return $result;
    }

}
