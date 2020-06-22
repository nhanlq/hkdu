<?php

namespace Drupal\drug_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\taxonomy\Entity\Term;

/**
 * Class DrugSearchListController.
 */
class DrugSearchListController extends ControllerBase {

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
        $search = NULL;
        if (isset($_GET['keys'])) {
            $search = $_GET['keys'];
        }
        $ind = NULL;
        if (isset($_GET['ind'])) {
            $ind = $_GET['ind'];
        }
        $ing = NULL;
        if (isset($_GET['ing'])) {
            $ing = $_GET['ing'];
        }
        $name = NULL;
        if (isset($_GET['name'])) {
            $name = $_GET['name'];
        }
        return [
            'results' => [
                '#theme' => 'drug_search_list',
                '#drug_search' => $this->getDrug(),
                '#tags' => $tags,
                '#keys' => $search,
                '#ind' => $ind,
                '#ing' => $ing,
                '#search' => $search,
                '#name' => $name,
            ],
            'pager' => [
                '#type' => 'pager',
            ],
        ];
    }

    public function getDrug()
    {
        $currentDate = time();

        if (isset($_GET['keys']) && !empty($_GET['keys'])) {

            $id1 = \Drupal::entityQuery('drug_search')
                ->condition('status', 1)
                ->condition('name', $_GET['keys'], 'CONTAINS')
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->sort('field_publish_date','DESC')
                ->pager(10)
                ->execute();
            $id2 = \Drupal::entityQuery('drug_search')
                ->condition('status', 1)
                ->condition('field_description', $_GET['keys'], 'CONTAINS')
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->sort('field_publish_date','DESC')
                ->pager(10)
                ->execute();
            $id3 = \Drupal::entityQuery('drug_search')
                ->condition('status', 1)
                ->condition('field_active_ingredients', $_GET['keys'], 'CONTAINS')
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->sort('field_publish_date','DESC')
                ->pager(10)
                ->execute();
            $id4 = \Drupal::entityQuery('drug_search')
                ->condition('status', 1)
                ->condition('field_indications', $_GET['keys'], 'CONTAINS')
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->sort('field_publish_date','DESC')
                ->pager(10)
                ->execute();
            $ids = array_merge($id1,$id2, $id3, $id4);
        }elseif (isset($_GET['name']) && !empty($_GET['name'])) {
            $ids = \Drupal::entityQuery('drug_search')
                ->condition('status', 1)
                ->condition('name', $_GET['name'], 'CONTAINS')
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->sort('field_publish_date','DESC')
                ->pager(10)
                ->execute();
        } elseif (isset($_GET['ing']) && !empty($_GET['ing'])) {
            $ids = \Drupal::entityQuery('drug_search')
                ->condition('status', 1)
                ->condition('field_active_ingredients', $_GET['ing'], 'CONTAINS')
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->sort('field_publish_date','DESC')
                ->pager(10)
                ->execute();
        }
        elseif (isset($_GET['ind']) && !empty($_GET['ind'])) {
            $ids = \Drupal::entityQuery('drug_search')
                ->condition('status', 1)
                ->condition('field_indications', $_GET['ind'], 'CONTAINS')
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->sort('field_publish_date','DESC')
                ->pager(10)
                ->execute();
        }elseif(isset($_GET['tags']) && !empty($_GET['tags'])){
            $tid = $this->getTagsTid($_GET['tags']);
            $ids = \Drupal::entityQuery('drug_search')
                ->condition('status', 1)
                ->condition('field_drug_classification', $tid)
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->sort('field_publish_date','DESC')
                ->pager(10)
                ->execute();
        }
        else {
            $ids = \Drupal::entityQuery('drug_search')
                ->condition('status', 1)
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->sort('field_publish_date','DESC')
                ->pager(10)
                ->execute();
        }
        $result = \Drupal\drug_search\Entity\DrugSearch::loadMultiple($ids);
        return $result;
    }

    public function getTags()
    {
        $tags = [];
        $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('drug');
        foreach ($terms as $term) {
            $tags[$term->tid] = $term->name;
        }
        return $tags;
    }

    public function getTagsTid($name)
    {
        $term = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties(['name' => $name, 'vid' => 'drug']);
        $term = reset($term);
        $term_id = $term->id();
        return $term_id;
    }
    public function title(){
        return ['#markup' => \Drupal::state()->get('/e-pharm/drug-search','Drug Search'), '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList()];
    }

}
