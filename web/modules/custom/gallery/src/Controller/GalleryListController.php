<?php

namespace Drupal\gallery\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\gallery\Entity\Gallery;

/**
 * Class GalleryListController.
 */
class GalleryListController extends ControllerBase {

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
                '#theme' => 'gallery_list',
                '#gallerys' => $this->getGallerys(),
                '#taxonomy' => $this->getTermCategory()
            ],
//            'pager' => [
//                '#type' => 'pager',
//            ],
        ];
    }

    public function getGallerys()
    {

        $ids = \Drupal::entityQuery('gallery')
            ->condition('status', 1)
          //  ->condition('field_category','NULL')
            ->sort('field_date','DESC')
            ->sort('field_weight','ASC')
           // ->pager(12)
            ->execute();
      //  var_dump($ids);die;
        $data = [];
        $result = Gallery::loadMultiple($ids);
        foreach($result as $gallery){
          if($gallery->get('field_category')->target_id <= 0){
              $data[] =  $gallery;
          }
        }
        return $data;
    }
    public function getTermCategory(){
        $vid = 'gallery';
        $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
        $taxonomy = [];
        foreach ($terms as $term) {
            $taxonomy[$term->tid] = \Drupal\taxonomy\Entity\Term::load($term->tid);
        }

        return $taxonomy;
    }
    public function title(){
        return ['#markup' => \Drupal::state()->get('/gallery','Gallery'), '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList()];

    }

}
