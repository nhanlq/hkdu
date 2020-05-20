<?php

namespace Drupal\download\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class DownloadListController.
 */
class DownloadListController extends ControllerBase {

  /**
   * List.
   *
   * @return string
   *   Return Hello string.
   */
    public function list() {
        return [
            'results' => [
                '#theme' => 'download_list',
                '#downloads' => $this->getExternalLink(),
                '#paragraphs' => $paragraph,
            ],
            'pager' => [
                '#type' => 'pager',
            ],
        ];
    }

    public function getExternalLink()
    {

        $ids = \Drupal::entityQuery('download')
            ->condition('status', 1)
            ->sort('field_weight','ASC')
            ->sort('changed','DESC')
            ->pager(10)
            ->execute();
        $result = \Drupal\download\Entity\Download::loadMultiple($ids);
        foreach($result as $r){
            $paragraps = [];
            foreach($r->get('field_media') as $media){
                $para = \Drupal\paragraphs\Entity\Paragraph::load($media->target_id);
                $paragraps[] = $para;
            }
            $r->paragraphs = $paragraps;
        }
        return $result;
    }
    public function title(){
        return ['#markup' => \Drupal::state()->get('/downloads','Downloads'), '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList()];
    }

}
