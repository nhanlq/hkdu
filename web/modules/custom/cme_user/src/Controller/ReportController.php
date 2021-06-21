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
      $from = strtotime($period);
      $to = strtotime($period) + 31449600;
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
      $from = strtotime($period);
      $to = strtotime($period) + 31449600;
    }
    else {
      $fromto = explode('+', $period);
      $from = strtotime($fromto[0]);
      $to = strtotime($fromto[1]);
    }
    $ids = \Drupal::entityQuery('score')
      ->condition('status', 1)
      ->condition('field_user', $uid)
      ->condition('created', [$from, $to], 'BETWEEN')
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
    if ($period == '1st') {
      if ($total > 20) {
        return 20;
      }
    }
    if ($period == '2nd') {
      if ($total > 40) {
        return 40;
      }
    }
    if ($period == '3rd') {
      if ($total > 60) {
        return 60;
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
        if ($total >= 20) {
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
  public function getAllUsersMember($type, $period) {

    $users = $this->getUserScore($type, $period);
    foreach ($users as $user) {
      if ($type == 'period') {
        $start = strtotime($period);
        $end = strtotime($period) + 31449600;
      }
      $data[$user->id()] = [
        'hkdu_membership_no' => $user->get('field_registration_no')->value,
        'mchk_no' => $user->get('field_mchk_license')->value,
        'member_name' => $user->get('field_first_name')->value,
        'mce_cycle_start' => $start,
        'complete_year' => $end,
        'lecture' => $this->getTotalLecture($user->id(), $type, $period),
        'self_study' => $this->getTotalStudy($user->id(), $type, $period),
        'total' => $this->getTotalScore($user->id(), $type, $period),
      ];
    }
    return $data;
  }

  public function getUserScore($type, $period) {
    $start = strtotime($period);
    $end = strtotime($period) + 31449600;
    $ids = \Drupal::entityQuery('score')
      ->condition('status', 1)
      ->condition('created', [$start, $end], 'BETWEEN')
      ->execute();
    $result = \Drupal\cme_score\Entity\Score::loadMultiple($ids);
    $user = [];
    foreach ($result as $score) {
      $user[$score->get('field_user')->target_id] = \Drupal\user\Entity\User::load($score->get('field_user')->target_id);
    }
    return $user;
  }

}

