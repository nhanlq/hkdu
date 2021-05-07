<?php

namespace Drupal\member_profile\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EventCalendarController.
 */
class EventCalendarController extends ControllerBase {

  /**
   * Event.
   *
   * @return string
   *   Return Hello string.
   */
  public function event() {
    $event = $this->getEvent();
    $response = new Response();
    $data = $response->setContent(json_encode($event));

    $date = date('Y-m-d');
    return [
      '#theme' => 'cme_event_calendar',
      '#events' => $data->getContent(),
      '#date' => $date,
    ];
  }

  public function getEvent() {

    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'event_calendar')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->execute();
    $result = \Drupal\node\Entity\Node::loadMultiple($ids);
    //    foreach($result as $node){
    //      $start = str_replace('-', '', $node->get('field_event_start_date')
    //        ->value);
    //      $start = str_replace(':', '', $start);
    //      $end = str_replace('-', '', $node->get('field_event_end_date')->value);
    //      $end = str_replace(':', '', $end);
    //      $name = $node->get('title')->value;
    //      $host = \Drupal::request()->getSchemeAndHttpHost();
    //      $link = $node->get('field_event_url')->uri;
    //      $node->google_calendar = 'https://calendar.google.com/calendar/u/0/r/eventedit?text='.$name.'&dates='.$start.'/'.$end.'&details=For+details,+link+here:+'.$link.'&sf=true&output=xml';
    //      $file = \Drupal\file\Entity\File::load($node->get('field_ics')->target_id);
    //      $node->download = file_create_url($file->getFileUri());
    //    }

    $data = [];

    foreach ($result as $key => $node) {
      $event_obj = new \stdClass();
      $start = str_replace('-', '',
        $node->get('field_event_start_date')->value);
      $start = str_replace(':', '', $start);
      $event_obj->title = $node->getTitle();
      $event_obj->start = $start;
      $options = ['absolute' => TRUE];

      $url_object = \Drupal\Core\Url::fromRoute('entity.node.canonical', [
        'node' => $key,
      ], $options)->toString();
      $event_obj->url = $url_object;
      $data[] = $event_obj;

    }
    return $data;
  }

  //  public function getTerm($uid){
  //    $vid = 'calendar_event';
  //    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
  //    $term_data = [];
  //    foreach ($terms as $term) {
  //      $term_data[] = array(
  //        'id' => $term->tid,
  //        'name' => $term->name,
  //        'event' => $this->getEvent($uid, $term->tid)
  //      );
  //    }
  //    return $term_data;
  //  }
}
