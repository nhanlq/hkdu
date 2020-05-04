<?php

namespace Drupal\clinical_focus\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\clinical_focus\Entity\ClinicalFocus;
use Drupal\taxonomy\Entity\Term;
/**
 * Class ClinicalListController.
 */
class ClinicalListController extends ControllerBase {

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
                '#theme' => 'clinical_focus_list',
                '#clinical_focus' => $this->getDrug(),
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
            $ids = \Drupal::entityQuery('clinical_focus')
                ->condition('status', 1)
                ->condition('field_tags', $tid)
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->sort('created','DESC')
                ->pager(15)
                ->execute();
        } elseif (isset($_GET['keys'])) {
            $ids = \Drupal::entityQuery('clinical_focus')
                ->condition('status', 1)
                ->condition('name', $_GET['keys'], 'CONTAINS')
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->sort('created','DESC')
                ->pager(15)
                ->execute();
        } else {
            $ids = \Drupal::entityQuery('clinical_focus')
                ->condition('status', 1)
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->sort('created','DESC')
                ->pager(15)
                ->execute();
        }
        $result = ClinicalFocus::loadMultiple($ids);
        return $result;
    }

    public function getTags()
    {
        $tags = [];
        $ids = \Drupal::entityQuery('clinical_focus')
            ->condition('status', 1)
            ->execute();
        $result = ClinicalFocus::loadMultiple($ids);
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

}
