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

    $ids = \Drupal::entityQuery('event')
      ->condition('field_member_area', 1)
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->execute();
    $result = \Drupal\event\Entity\Event::loadMultiple($ids);
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
        $node->get('field_start_date')->value);
      $start = str_replace(':', '', $start);
      $event_obj->title = $node->getName();
      $event_obj->start = $start;
      $options = ['absolute' => TRUE];

      $url_object = \Drupal\Core\Url::fromRoute('member_profile.event_calendar_controller_event_detail', [
        'id' => $key,
      ], $options)->toString();
      $event_obj->url = $url_object;
      $data[] = $event_obj;

    }
    return $data;
  }
  public function detail($id){
    $event = \Drupal\event\Entity\Event::load($id);
    $current_user = \Drupal::currentUser();
    $current_roles = $current_user->getRoles();
    $date = time();
    if ($date > $event->get('field_expired')->value) {
      throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
    }
    if ($event->get('field_audience')->getValue()) {
      $au = TRUE;
      foreach ($event->get('field_audience')->getValue() as $audience) {
        if (!in_array($audience['value'], $current_roles)) {
          $au = FALSE;
        }
      }
      foreach ($event->get('field_audience')->getValue() as $audience) {
        if (in_array($audience['value'], $current_roles)) {
          $au = TRUE;
        }
      }
      if (in_array('admins', $current_roles) || in_array('administrator',
          $current_roles)) {
        $au = TRUE;
      }

    }
    $user = $event->getOwner();
    if (in_array('administrator', $current_roles)) {
      $au = TRUE;
    }
    if ($user->id() == $current_user->id()) {
      $au = TRUE;
    }
    if (!$au) {
      throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
    }
    $roles = $user->getRoles();
    if (in_array('drug_suppliers', $current_roles)) {
      if (in_array('drug_suppliers',
          $roles) && $current_user->id() != $user->id()) {
        throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
      }
    }
    $options = ['absolute' => TRUE];
    $time = str_replace('-','',$event->get('field_start_date')->value).'T'.str_replace(':','',$event->get('field_start_time')->value).'/'.str_replace('-','',$event->get('field_date')->value).'T'.str_replace(':','',$event->get('field_end_time')->value);
    $url_object = \Drupal\Core\Url::fromRoute('entity.event.canonical', ['event' => $id], $options)->toString();
    $google = 'https://calendar.google.com/calendar/u/0/r/eventedit?text='.$event->getName().'&dates='.$time.'&details=For+details,+link+here:+'.$url_object.'&sf=true&output=xml';
    return [
      '#theme' => 'member_event_detail',
      '#event' => $event,
      '#product' => getProduct($id),
      '#check_event' => checkEventStatusUser($id),
      '#payment_upload' =>checkEventUpload($id),
      '#expired' => checkExpiredEvent($event),
      '#google_calendar'=>$google
    ];

  }
  public function title($id)
  {
    $event = \Drupal\event\Entity\Event::load($id);
    return ['#markup' => $event->getName(), '#allowed_tags' =>
      \Drupal\Component\Utility\Xss::getHtmlTagList()];
  }
}
