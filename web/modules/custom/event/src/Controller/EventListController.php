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
  public function list() {
    $tags = NULL;
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

  /**
   * @return \Drupal\Core\Entity\EntityBase[]|\Drupal\Core\Entity\EntityInterface[]|\Drupal\event\Entity\Event[]
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getEvent() {
    $tid = FALSE;
    if (isset($_GET['tags'])) {
      $tags = $_GET['tags'];
      $tid = $this->getTagsTid($tags);
    }
    $currentDate = time();

    if ($tid) {
      $ids = \Drupal::entityQuery('event')
        ->condition('status', 1)
        ->condition('field_tags', $tid)
        ->condition('field_expired', $currentDate, '>=')
        ->condition('id', $this->getDrugUsers(), 'NOT IN')
        ->sort('field_weight', 'ASC')
        ->sort('field_date', 'ASC')
        ->pager(15)
        ->execute();
    }
    elseif (isset($_GET['keys'])) {
      $ids = \Drupal::entityQuery('event')
        ->condition('status', 1)
        ->condition('name', $_GET['keys'], 'CONTAINS')
        ->condition('field_expired', $currentDate, '>=')
        ->condition('id', $this->getDrugUsers(), 'NOT IN')
        ->sort('field_weight', 'ASC')
        ->sort('field_date', 'ASC')
        ->pager(15)
        ->execute();
    }
    else {
      $ids = \Drupal::entityQuery('event')
        ->condition('status', 1)
        ->condition('field_expired', $currentDate, '>=')
        ->condition('id', $this->getDrugUsers(), 'NOT IN')
        ->sort('field_weight', 'ASC')
        ->sort('field_date', 'ASC')
        ->pager(15)
        ->execute();
    }
    $result = \Drupal\event\Entity\Event::loadMultiple($ids);
    foreach ($result as $r) {
      if ($this->checkExpiredEvent($r)) {
        $r->expired = TRUE;
      }
      else {
        $r->expired = FALSE;
      }
      if ($this->checkEventStatusUser($r->id())) {
        $r->check_event = TRUE;
      }
      else {
        $r->check_event = FALSE;
      }
    }
    return $result;
  }

  /**
   * @return array
   */
  public function getTags() {
    $tags = [];
    $ids = \Drupal::entityQuery('event')->condition('status', 1)->execute();
    $result = \Drupal\event\Entity\Event::loadMultiple($ids);
    foreach ($result as $drug) {
      foreach ($drug->get('field_tags')->getValue() as $tag) {
        $term = Term::load($tag['target_id']);
        $tags[$tag['target_id']] = $term->getName();
      }
    }
    return $tags;
  }

  /**
   * @param $name
   *
   * @return int|string|null
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getTagsTid($name) {
    $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties([
        'name' => $name,
        'vid' => 'event',
      ]);
    $term = reset($term);
    $term_id = $term->id();
    return $term_id;
  }


  /**
   * get product by Event
   */
  public function getProduct($event_id) {
    $ids = \Drupal::entityQuery('commerce_product')
      ->condition('type', 'default')
      ->condition('status', 1)
      ->condition('field_event', $event_id)
      ->execute();
    $results = \Drupal\commerce_product\Entity\Product::loadMultiple($ids);
    $result = reset($results);
    return $result;
  }

  /**
   * @param $event_id
   *
   * @return bool|null
   */
  public function checkEventStatusUser($event_id) {
    $return = NULL;
    $user = \Drupal::currentUser();
    $uid = $user->id();
    $ids = \Drupal::entityQuery('event_tracking')
      ->condition('status', 1)
      ->condition('field_event', $event_id)
      ->condition('field_user', $uid)
      ->execute();
    $results = \Drupal\event_tracking\Entity\EventTracking::loadMultiple($ids);
    if ($results) {
      $return = TRUE;
    }
    return $return;
  }

  /**
   * @param $event
   *
   * @return bool
   */
  public function checkExpiredEvent($event) {
    $current = time();
    $date = strtotime($event->get('field_date')->value . ' 00:00:00');
    if ($current > $date) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * @return array
   */
  public function title() {
    return [
      '#markup' => \Drupal::state()->get('/e-pharm/event', 'Events'),
      '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList(),
    ];
  }

  /**
   * @param $id
   *
   * @return array
   */
  public function list_title($id) {
    $event = \Drupal\event\Entity\Event::load($id);
    return [
      '#markup' => $event->get('name')->value,
      '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList(),
    ];
  }

  /**
   * @param $id
   *
   * @return \Drupal\Core\Entity\EntityBase[]|\Drupal\Core\Entity\EntityInterface[]|\Drupal\event_tracking\Entity\EventTracking[]|false
   */
  public function enrollment($id){
    $ids = \Drupal::entityQuery('event_tracking')
      ->condition('field_event', $id)
      ->execute();
    $results = \Drupal\event_tracking\Entity\EventTracking::loadMultiple($ids);
    if ($results) {
      $scores = $results;
    }
    return [
      '#theme' => 'event_enrollment_list',
      '#scores' => $scores,
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  /**
   * get all drug users
   */
  public function getDrugUsers() {
    $user = \Drupal::currentUser();
    $uids = [];
    $id = [];

    if (in_array('drug_suppliers', $user->getRoles())) {
      $uid = $user->id();
    }
    else {
      $uid = FALSE;
    }
    if ($uid) {
      $ids = \Drupal::entityQuery('user')
        ->condition('status', 1)
        ->condition('uid', $uid, '<>')
        ->condition('roles', 'drug_suppliers')
        ->execute();
      $users = \Drupal\user\Entity\User::loadMultiple($ids);
      foreach ($users as $user) {
        $uids[] = $user->id();
      }
      $spd = \Drupal::entityQuery('event')
        ->condition('status', 1)
        ->condition('user_id', $uids, 'IN')
        ->execute();
      $result = \Drupal\event\Entity\Event::loadMultiple($spd);
      if ($result) {
        foreach ($result as $sp) {
          $id[] = $sp->id();
        }
      }
      else {
        $id[] = 0;
      }

    }
    else {
      $id[] = 0;
    }
    return $id;
  }
}
