<?php

namespace Drupal\special_offer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\special_offer\Entity\SpecialOffer;
use Drupal\taxonomy\Entity\Term;

/**
 * Class SpecialListController.
 */
class SpecialListController extends ControllerBase {

  /**
   * List.
   *
   * @return string
   *   Return Hello string.
   */
    public function list()
    {
        $tags = null;
        if (isset($_GET['tags'])) {
            $tags = $_GET['tags'];
        }
        $search = '';
        if (isset($_GET['keys'])) {
            $search = $_GET['keys'];
        }
        return [
            'results' => [
                '#theme' => 'special_offer_list',
                '#special_offer' => $this->getDrug(),
                '#tags' => $this->getTags(),
                '#get' => $tags,
                '#search' => $search,
            ],
            'pager' => [
                '#type' => 'pager',
            ],
        ];
    }

    public function getDrug()
    {
        $tid = False;
        if (isset($_GET['tags'])) {
            $tags = $_GET['tags'];
            $tid = $this->getTagsTid($tags);
        }


        if ($tid) {
            $ids = \Drupal::entityQuery('special_offer')
                ->condition('status', 1)
                ->condition('field_tags', $tid)
                ->sort('field_weight','ASC')
                ->sort('field_publish_date','DESC')
                ->pager(10)
                ->execute();
        } elseif (isset($_GET['keys'])) {
            $ids = \Drupal::entityQuery('special_offer')
                ->condition('status', 1)
                ->condition('name', $_GET['keys'], 'CONTAINS')
                ->sort('field_weight','ASC')
                ->sort('field_publish_date','DESC')
                ->pager(10)
                ->execute();
        } else {
            $ids = \Drupal::entityQuery('special_offer')
                ->condition('status', 1)
                ->sort('field_weight','ASC')
                ->sort('field_publish_date','DESC')
                ->pager(10)
                ->execute();
        }
        $result = SpecialOffer::loadMultiple($ids);
        return $result;
    }

    public function getTags()
    {
        $tags = [];
        $ids = \Drupal::entityQuery('special_offer')
            ->condition('status', 1)
            ->execute();
        $result = SpecialOffer::loadMultiple($ids);
        foreach ($result as $drug) {
            foreach ($drug->get('field_tags')->getValue() as $tag) {
                $term = Term::load($tag['target_id']);
                $tags[$tag['target_id']] = $term->getName();
            }
        }
        return $tags;
    }

    public function getTagsTid($name)
    {
        $term = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties(['name' => $name, 'vid' => 'epharm_tags']);
        $term = reset($term);
        $term_id = $term->id();
        return $term_id;
    }
    public function title(){
        return ['#markup' => \Drupal::state()->get('/e-pharm/special-offer','Special Offers'), '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList()];
    }

}
