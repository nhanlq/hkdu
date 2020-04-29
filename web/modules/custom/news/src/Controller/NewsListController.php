<?php

namespace Drupal\news\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class NewsListController.
 */
class NewsListController extends ControllerBase {

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
                '#theme' => 'news_list',
                '#news' => $this->getDrug(),
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
            $ids = \Drupal::entityQuery('news')
                ->condition('status', 1)
                ->condition('field_tags', $tid)
                ->pager(15)
                ->execute();
        } elseif (isset($_GET['keys'])) {
            $ids = \Drupal::entityQuery('news')
                ->condition('status', 1)
                ->condition('name', $_GET['keys'], 'CONTAINS')
                ->pager(15)
                ->execute();
        } else {
            $ids = \Drupal::entityQuery('news')
                ->condition('status', 1)
                ->pager(15)
                ->execute();
        }
        $result = \Drupal\news\Entity\News::loadMultiple($ids);
        return $result;
    }

    public function getTags()
    {
        $tags = [];
        $ids = \Drupal::entityQuery('news')
            ->condition('status', 1)
            ->execute();
        $result = \Drupal\news\Entity\News::loadMultiple($ids);
        foreach ($result as $drug) {
            foreach ($drug->get('field_tags')->getValue() as $tag) {
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

}
