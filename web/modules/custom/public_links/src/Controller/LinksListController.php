<?php

namespace Drupal\public_links\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class LinksListController.
 */
class LinksListController extends ControllerBase {

  /**
   * List.
   *
   * @return string
   *   Return Hello string.
   */
    public function list() {
        return [
            'results' => [
                '#theme' => 'public_links_list',
                '#public_links' => $this->getExternalLink(),
            ],
            'pager' => [
                '#type' => 'pager',
            ],
        ];
    }

    public function getExternalLink()
    {

        $ids = \Drupal::entityQuery('public_links')
            ->condition('status', 1)
            ->sort('changed','DESC')
            ->pager(10)
            ->execute();
        $result = \Drupal\public_links\Entity\PublicLinks::loadMultiple($ids);
        return $result;
    }
    public function title(){
        return ['#markup' => \Drupal::state()->get('/links','Links'), '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList()];
    }

}
