<?php

namespace Drupal\drug_news\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\drug_news\Entity\DrugNews;
use Drupal\taxonomy\Entity\Term;

/**
 * Class DrugNewsListController.
 */
class DrugNewsListController extends ControllerBase
{

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
                '#theme' => 'drug_news_list',
                '#drug_news' => $this->getDrug(),
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
        $currentDate = time();

        if ($tid) {
            $ids = \Drupal::entityQuery('drug_news')
                ->condition('status', 1)
                ->condition('field_tags', $tid)
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->sort('created','DESC')
                ->pager(15)
                ->execute();
        } elseif (isset($_GET['keys'])) {
            $ids = \Drupal::entityQuery('drug_news')
                ->condition('status', 1)
                ->condition('field_expired',$currentDate,'>=')
                ->condition('name', $_GET['keys'], 'CONTAINS')
                ->sort('field_weight','ASC')
                ->sort('created','DESC')
                ->pager(15)
                ->execute();
        } else {
            $ids = \Drupal::entityQuery('drug_news')
                ->condition('status', 1)
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->sort('created','DESC')
                ->pager(15)
                ->execute();
        }
        $result = DrugNews::loadMultiple($ids);
        return $result;
    }

    public function getTags()
    {
        $tags = [];
        $ids = \Drupal::entityQuery('drug_news')
            ->condition('status', 1)
            ->execute();
        $result = DrugNews::loadMultiple($ids);
        if($result){
            foreach ($result as $drug) {
                foreach ($drug->get('field_tags')->getValue() as $tag) {
                    $term = Term::load($tag['target_id']);
                    if($term){
                        $tags[$tag['target_id']] = $term->getName();
                    }

                }
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
        if($term){
            $term_id = $term->id();
            return $term_id;
        }

    }

    public function title(){
        return ['#markup' => \Drupal::state()->get('/e-pharm/drug-news','Drug News'), '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList()];
    }


}
