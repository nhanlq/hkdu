<?php

namespace Drupal\gallery\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class CategoryController.
 */
class CategoryController extends ControllerBase
{

    /**
     * Category.
     *
     * @return string
     *   Return Hello string.
     */
    public function category($id)
    {
        $current_path = \Drupal::service('path.current')->getPath();
        $path = explode('/', $current_path);
        $id = $path[3];
        return [
            'results' => [
                '#theme' => 'gallery_list',
                '#gallerys' => $this->getGallerys($id),
                '#taxonomy' => null
            ],
//            'pager' => [
//                '#type' => 'pager',
//            ],
        ];

    }

    public function getGallerys($id)
    {

        $ids = \Drupal::entityQuery('gallery')
            ->condition('status', 1)
            ->condition('field_category',$id)
            ->sort('field_date','DESC')
            ->sort('field_weight','ASC')
            // ->pager(12)
            ->execute();
        $result = \Drupal\gallery\Entity\Gallery::loadMultiple($ids);
        return $result;
    }

    public function title()
    {
        $current_path = \Drupal::service('path.current')->getPath();
        $path = explode('/', $current_path);
        $id = $path[3];
        $term = \Drupal\taxonomy\Entity\Term::load($id);
        return ['#markup' => $term->getName(), '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList()];

    }

}
