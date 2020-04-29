<?php

namespace Drupal\external_link\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class ExternalLinkListController.
 */
class ExternalLinkListController extends ControllerBase {

  /**
   * List.
   *
   * @return string
   *   Return Hello string.
   */
    public function list() {
        return [
            'results' => [
                '#theme' => 'external_link_list',
                '#external_link' => $this->getExternalLink(),
            ],
            'pager' => [
                '#type' => 'pager',
            ],
        ];
    }

    public function getExternalLink()
    {

        $ids = \Drupal::entityQuery('external_link')
            ->condition('status', 1)
            ->sort('field_weight','ASC')
            ->pager(10)
            ->execute();
        $result = \Drupal\external_link\Entity\ExternalLink::loadMultiple($ids);
        return $result;
    }

}
