<?php

namespace Drupal\about\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\about\Entity\DefaultEntity;

/**
 * Class AboutController.
 */
class AboutController extends ControllerBase
{

    /**
     * List.
     *
     * @return string
     *   Return Hello string.
     */
    public function list()
    {
        return array(
            'abouts' => [
                '#theme' => array('about_list'),
                '#abouts' => $this->getAllAbout(),
            ],

            'pager' => [
                '#type' => 'pager',
            ],
          '#cache' => [
            'max-age' => 0,
          ],
        );
    }

    public function getAllAbout()
    {
        $ids = \Drupal::entityQuery('about')
            ->condition('status', 1)
            ->sort('field_weight', 'ASC')
            ->sort('field_publish_date', 'DESC')
            ->pager(10)
            ->execute();
        $result = DefaultEntity::loadMultiple($ids);
        return $result;
    }

    public function aboutTitle()
    {
        return ['#markup' => \Drupal::state()->get('/about-us', 'About Us'), '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList()];
    }

}
