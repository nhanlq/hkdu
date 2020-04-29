<?php

namespace Drupal\event\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\taxonomy\Entity\Term;


/**
 * Class EventListController.
 */
class EventListController extends ControllerBase {

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
                '#theme' => 'event_list',
                '#events' => $this->getEvent(),
                '#tags' => $this->getTags(),
                '#get' => $tags,
                '#search' => $search,
            ],
            'pager' => [
                '#type' => 'pager',
            ],
        ];
    }

    public function getEvent()
    {
        $tid = False;
        if (isset($_GET['tags'])) {
            $tags = $_GET['tags'];
            $tid = $this->getTagsTid($tags);
        }
        $currentDate = time();

        if ($tid) {
            $ids = \Drupal::entityQuery('event')
                ->condition('status', 1)
                ->condition('field_tags', $tid)
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->pager(15)
                ->execute();
        } elseif (isset($_GET['keys'])) {
            $ids = \Drupal::entityQuery('event')
                ->condition('status', 1)
                ->condition('name', $_GET['keys'], 'CONTAINS')
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->pager(15)
                ->execute();
        } else {
            $ids = \Drupal::entityQuery('event')
                ->condition('status', 1)
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->pager(15)
                ->execute();
        }
        $result = \Drupal\event\Entity\Event::loadMultiple($ids);
        foreach($result as $r){
            if($this->checkExpiredEvent($r)){
                $r->expired = true;
            }else{
                $r->expired = false;
            }
            if($this->checkEventStatusUser($r->id())){
                $r->check_event = true;
            }else{
                $r->check_event = false;
            }
        }
        return $result;
    }

    public function getTags()
    {
        $tags = [];
        $ids = \Drupal::entityQuery('event')
            ->condition('status', 1)
            ->execute();
        $result = \Drupal\event\Entity\Event::loadMultiple($ids);
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
            ->loadByProperties(['name' => $name, 'vid' => 'event']);
        $term = reset($term);
        $term_id = $term->id();
        return $term_id;
    }


    /**
     * get product by Event
     */
    public function getProduct($event_id){
        $ids = \Drupal::entityQuery('commerce_product')
            ->condition('type', 'default')
            ->condition('status', 1)
            ->condition('field_event', $event_id)
            ->execute();
        $results = \Drupal\commerce_product\Entity\Product::loadMultiple($ids);
        $result = reset($results);
        return $result;
    }

    public function checkEventStatusUser($event_id){
        $return = null;
        $user = \Drupal::currentUser();
        $uid = $user->id();
        $ids = \Drupal::entityQuery('event_tracking')
            ->condition('status', 1)
            ->condition('field_event', $event_id)
            ->condition('field_user', $uid)
            ->execute();
        $results = \Drupal\event_tracking\Entity\EventTracking::loadMultiple($ids);
        if($results){
            $return = true;
        }
        return $return;
    }

    public function checkExpiredEvent($event){
        $current = time();
        $date = strtotime($event->get('field_date')->value.' 00:00:00');
        if($current > $date){
            return true;
        }else{
            return false;
        }
    }
}
