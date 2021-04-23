<?php

namespace Drupal\member_profile\Controller;

use Drupal\Core\Controller\ControllerBase;

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
  public function event($uid) {
    $data = [];

    return array(
      'event' => [
        '#theme' => array('member_event'),
        '#event' => $this->getTerm($uid),
        '#uri' => \Drupal::service('path.current')->getPath(),
        '#uid' => $uid,
        '#current_uid' => \Drupal::currentUser()->id(),
      ],
    );
  }
  public function getEvent($uid, $tid)
  {

    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'event_calendar')
      ->condition('status', 1)
      ->condition('field_category', $tid)
      ->condition('uid', $uid)
      ->sort('created', 'DESC')
      ->execute();
    $result = \Drupal\node\Entity\Node::loadMultiple($ids);
    foreach($result as $node){
      $start = str_replace('-', '', $node->get('field_event_start_date')
        ->value);
      $start = str_replace(':', '', $start);
      $end = str_replace('-', '', $node->get('field_event_end_date')->value);
      $end = str_replace(':', '', $end);
      $name = $node->get('title')->value;
      $host = \Drupal::request()->getSchemeAndHttpHost();
      $link = $node->get('field_event_url')->uri;
      $node->google_calendar = 'https://calendar.google.com/calendar/u/0/r/eventedit?text='.$name.'&dates='.$start.'/'.$end.'&details=For+details,+link+here:+'.$link.'&sf=true&output=xml';
      $file = \Drupal\file\Entity\File::load($node->get('field_ics')->target_id);
      $node->download = file_create_url($file->getFileUri());
    }
    return $result;
  }

  public function getTerm($uid){
    $vid = 'calendar_event';
    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
    $term_data = [];
    foreach ($terms as $term) {
      $term_data[] = array(
        'id' => $term->tid,
        'name' => $term->name,
        'event' => $this->getEvent($uid, $term->tid)
      );
    }
    return $term_data;
  }
}
