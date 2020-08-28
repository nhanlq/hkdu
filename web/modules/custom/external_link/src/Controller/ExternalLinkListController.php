<?php

namespace Drupal\external_link\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class ExternalLinkListController.
 */
class ExternalLinkListController extends ControllerBase
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
                '#theme' => 'external_link_list',
                '#external_link' => $this->getExternalLink(),
                '#tags' => $this->getTags(),
                '#get' => $tags,
                '#search' => $search,
            ],
            'pager' => [
                '#type' => 'pager',
            ],
        ];
    }

    public function getExternalLink()
    {
        $tid = False;
        if (isset($_GET['tags'])) {
            $tags = $_GET['tags'];
            $tid = $this->getTagsTid($tags);
        }


        if ($tid) {
            $ids = \Drupal::entityQuery('external_link')
                ->condition('status', 1)
                ->condition('field_tags', $tid)
                ->sort('field_weight', 'ASC')
                ->sort('created', 'DESC')
            //    ->pager(10)
                ->execute();
        } elseif (isset($_GET['keys'])) {
            $ids = \Drupal::entityQuery('external_link')
                ->condition('status', 1)
                ->condition('name', $_GET['keys'], 'CONTAINS')
                ->sort('field_weight', 'ASC')
                ->sort('created', 'DESC')
             //   ->pager(10)
                ->execute();

        } else {
            $ids = \Drupal::entityQuery('external_link')
                ->condition('status', 1)
                ->sort('field_weight', 'ASC')
                ->sort('created', 'DESC')
             //   ->pager(10)
                ->execute();
        }
        $result = \Drupal\external_link\Entity\ExternalLink::loadMultiple($ids);
        $data = [];
        $vid = 'useful_link';
        $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
        foreach ($terms as $term) {
            $link_data = [];
            foreach($result as $id => $link){
                if($term->tid == $link->get('field_category')->target_id){
                    $link_data[$id]= $link;
                    $data[$term->tid] = ['cate'=>$term->name, 'links' => $link_data];
                }
            }

        }

        //var_dump($data);die;
        return $data;
    }

    public function getTags()
    {
        $tags = [];
        $ids = \Drupal::entityQuery('external_link')
            ->condition('status', 1)
            ->execute();
        $result = \Drupal\external_link\Entity\ExternalLink::loadMultiple($ids);
        foreach ($result as $drug) {
            foreach ($drug->get('field_tags')->getValue() as $tag) {
                $term = \Drupal\taxonomy\Entity\Term::load($tag['target_id']);
                $tags[$tag['target_id']] = $term->getName();
            }
        }
      //  var_dump($tags);die;
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

    public function title()
    {
        return ['#markup' => \Drupal::state()->get('/e-pharm/external-links', 'External Links'), '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList()];
    }

}
