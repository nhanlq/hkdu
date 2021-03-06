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
                '#theme' => 'public_links_list',
                '#data' => $this->getExternalLink(),
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
            $ids = \Drupal::entityQuery('public_links')
                ->condition('status', 1)
                ->condition('field_tags', $tid)
                ->sort('field_weight', 'ASC')
                ->sort('created', 'DESC')
             //   ->pager(20)
                ->execute();
        } elseif (isset($_GET['keys'])) {
            $ids = \Drupal::entityQuery('public_links')
                ->condition('status', 1)
                ->condition('name', $_GET['keys'], 'CONTAINS')
                ->sort('field_weight', 'ASC')
                ->sort('created', 'DESC')
              //  ->pager(20)
                ->execute();
        } else {
            $ids = \Drupal::entityQuery('public_links')
                ->condition('status', 1)
                ->sort('field_weight', 'ASC')
                ->sort('created', 'DESC')
               // ->pager(20)
                ->execute();
        }
        $result = \Drupal\public_links\Entity\PublicLinks::loadMultiple($ids);
        $data = [];
        $vid = 'link';
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
        $ids = \Drupal::entityQuery('public_links')
            ->condition('status', 1)
            ->execute();
        $result = \Drupal\public_links\Entity\PublicLinks::loadMultiple($ids);
        foreach ($result as $link) {
            foreach ($link->get('field_tags')->getValue() as $tag) {
                $term = \Drupal\taxonomy\Entity\Term::load($tag['target_id']);
                $tags[$tag['target_id']] = $term->getName();
            }
        }
        return $tags;
    }

    public function getTagsTid($name)
    {
        $term = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties(['name' => $name, 'vid' => 'tags']);
        $term = reset($term);
        $term_id = $term->id();
        return $term_id;
    }

    public function title(){
        return ['#markup' => \Drupal::state()->get('/links','Links'), '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList()];
    }

}
