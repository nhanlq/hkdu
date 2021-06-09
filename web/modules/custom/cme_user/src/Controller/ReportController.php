<?php

namespace Drupal\cme_user\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Class ReportController.
 */
class ReportController extends ControllerBase {

  /**
   * Report.
   *
   * @return string
   *   Return Hello string.
   */
  public function report($uid, $type, $period) {
    $user = \Drupal\user\Entity\User::load($uid);
    if ($type == 'period') {
      $date = explode('-', $user->get('field_cme_join_date')->value);
      if ($period == '1st') {
        if ($date[0] < date('Y') && (date('Y') - $date[0]) == 2) {
          $from = $user->get('field_cme_join_date')->value;
          $to = $date[0] . '-12-31';
        }
      }
      if ($period == '2nd') {
        if ($date[0] < date('Y') && (date('Y') - ($date[0] + 1)) == 1) {
          $from = strtotime(str_replace($date[0], $date[0] + 1, $user->get('field_cme_join_date')->value));
          $to = strtotime(($date[0] + 1) . '-12-31');
        }
      }
      if ($period == '3rd') {
        $from = strtotime(str_replace($date[0], $date[0] + 2, $user->get('field_cme_join_date')->value));
        $to = strtotime((date('Y')) . '-12-31');
      }
    }
    else {
      $fromto = explode('+', $period);
      $from = $fromto[0];
      $to = $fromto[1];
    }

    return [
      '#theme' => 'user_report',
      '#scores' => $this->getRecordDetail($uid, $type, $period),
      '#user' => $user,
      '#total' => $this->getTotalScore($uid, $type, $period),
      '#total_study' => $this->getTotalStudy($uid, $type, $period),
      '#total_lecture' => $this->getTotalLecture($uid, $type, $period),
      '#type' => $type,
      '#from' => $from,
      '#to' => $to,
      '#now' => date('Y-m-d'),
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  /**
   * @param $uid
   * @param $type
   * @param $period
   *
   * @return \Drupal\cme_score\Entity\Score[]|\Drupal\Core\Entity\EntityBase[]|\Drupal\Core\Entity\EntityInterface[]
   */
  public function getResultUser($uid, $type, $period) {
    $user = \Drupal\user\Entity\User::load($uid);

    if ($type == 'period') {
      $date = explode('-', $user->get('field_cme_join_date')->value);
      if ($period == '1st') {
        if ($date[0] < date('Y') && (date('Y') - $date[0]) == 2) {
          $start = strtotime($user->get('field_cme_join_date')->value);
          $end = strtotime($date[0] . '-12-31');
        }
      }
      if ($period == '2nd') {
        if ($date[0] < date('Y') && (date('Y') - $date[0]) == 1) {
          $start = strtotime(str_replace($date[0], $date[0] + 1, $user->get('field_cme_join_date')->value));
          $end = strtotime(($date[0] + 1) . '-12-31');
        }
      }
      if ($period == '3rd') {
        $start = strtotime(str_replace($date[0], $date[0] + 2, $user->get('field_cme_join_date')->value));
        $end = time();
      }
    }
    else {
      $fromto = explode('+', $period);
      $start = strtotime($fromto[0]);
      $end = strtotime($fromto[1]);
    }
    $ids = \Drupal::entityQuery('score')
      ->condition('status', 1)
      ->condition('field_user', $uid)
      ->condition('created', [$start, $end], 'BETWEEN')
      ->execute();
    $result = \Drupal\cme_score\Entity\Score::loadMultiple($ids);
    return $result;
  }

  /**
   * @param $uid
   * @param $type
   * @param $period
   *
   * @return int
   */
  public function getTotalStudy($uid, $type, $period) {
    $scores = $this->getResultUser($uid, $type, $period);
    $total = 0;
    foreach ($scores as $score) {
      if ($score->get('field_score_type')->value == 'Self-Study') {
        $total += $score->get('field_score')->value;
      }
    }
    if ($total > 20) {
      return 20;
    }
    else {
      return $total;
    }
  }

  /**
   * @param $uid
   * @param $type
   * @param $period
   *
   * @return int
   */
  public function getTotalLecture($uid, $type, $period) {

    $scores = $this->getResultUser($uid, $type, $period);
    $total = 0;
    foreach ($scores as $score) {
      if ($score->get('field_score_type')->value == 'Lecture') {
        $total += $score->get('field_score')->value;
      }
    }
    return $total;

  }

  /**
   * @param $uid
   * @param $type
   * @param $period
   *
   * @return int
   */
  public function getTotalScore($uid, $type, $period) {
    $total = $this->getTotalStudy($uid, $type, $period) + $this->getTotalLecture($uid, $type, $period);
    return $total;
  }

  /**
   * @param $uid
   * @param $from
   * @param $to
   *
   * @return array
   */
  public function getRecordDetail($uid, $from, $to) {
    $data = [];

    $scores = $this->getResultUser($uid, $from, $to);
    $total = 0;
    foreach ($scores as $score) {
      if ($score->get('field_score_type')->value == 'Lecture') {
        if ($score->get('field_quiz')->target_id > 0) {
          $quiz = \Drupal\cme_quiz\Entity\Quiz::load($score->get('field_quiz')->target_id);
          $data['lecture'][] = [
            'date' => $score->get('created')->value,
            'name' => $quiz->get('name')->value,
            'score' => $score->get('field_score')->value,
            'organizer' => $quiz->get('field_organiser')->value,
          ];
        }
        elseif ($score->get('field_event')->target_id > 0) {
          $event = \Drupal\cme_event\Entity\CmeEvent::load($score->get('field_event')->target_id);
          $data['lecture'][] = [
            'date' => $score->get('created')->value,
            'name' => $event->get('name')->value,
            'score' => $score->get('field_score')->value,
            'organizer' => $this->getCategory($event->get('field_organizer')->target_id),
          ];
        }
      }
      if ($score->get('field_score_type')->value == 'Self-Study') {
        $total += $score->get('field_score')->value;
        if ($total > 20) {
          break;
        }
        if ($score->get('field_quiz')->target_id > 0) {
          $quiz = \Drupal\cme_quiz\Entity\Quiz::load($score->get('field_quiz')->target_id);
          $data['self_study'][] = [
            'date' => $score->get('created')->value,
            'name' => $quiz->get('name')->value,
            'score' => $score->get('field_score')->value,
            'organizer' => $quiz->get('field_organiser')->value,
          ];
        }
        elseif ($score->get('field_event')->target_id > 0) {
          $event = \Drupal\cme_event\Entity\CmeEvent::load($score->get('field_event')->target_id);
          $data['self_study'][] = [
            'date' => $score->get('created')->value,
            'name' => $event->get('name')->value,
            'score' => $score->get('field_score')->value,
            'organizer' => $this->getCategory($event->get('field_organizer')->target_id),
          ];
        }
      }


    }
    return $data;
  }

  /**
   * @param $name
   *
   * @return int|string|null
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getCategory($tid) {
    $term = \Drupal\taxonomy\Entity\Term::load($tid);
    return $term->getName();
  }

  public function admin_report($type, $period) {

    return [
      '#theme' => 'admin_user_report',
      '#data' => $this->getAllUsersMember($type, $period),
      '#period' => $period,
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  /**
   * @param $type
   * @param $period
   */
  public function getAllUsersMember($type, $period){
    $ids = \Drupal::entityQuery('user')
      ->condition('roles', ['hkdu_members','cme_member','doctor','council_members','drug_suppliers'], 'IN')
      ->condition('status',1)
      ->execute();
    $users = \Drupal\user\Entity\User::loadMultiple($ids);
    $data = [];

    foreach($users as $user){
      $to = NULL;
      if ($type == 'period') {
        $date = explode('-', $user->get('field_cme_join_date')->value);
        if ($period == '1st') {
          if ($date[0] < date('Y') && (date('Y') - $date[0]) == 2) {
            $to = $date[0] . '-12-31';
          }
        }
        if ($period == '2nd') {
          if ($date[0] < date('Y') && (date('Y') - ($date[0] + 1)) == 1) {
            $to = ($date[0] + 1) . '-12-31';
          }
        }
        if ($period == '3rd') {
          $to = (date('Y')) . '-12-31';
        }
      }
      $data[] = [
        'hkdu_membership_no'=> $user->get('field_registration_no')->value,
        'mchk_no' => $user->get('field_mchk_license')->value,
        'member_name' => $user->get('field_first_name')->value,
        'mce_cycle_start' => $user->get('field_cme_join_date')->value,
        'complete_year' => $to,
        'lecture' => $this->getTotalLecture($user->id(), $type, $period),
        'self_study' => $this->getTotalStudy($user->id(), $type, $period),
        'total' => $this->getTotalScore($user->id(), $type, $period),
      ];
    }
    return $data;
  }

}
