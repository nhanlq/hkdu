<?php

namespace Drupal\cme_user\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\config_pages\Entity\ConfigPages;

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
      $time = $this->getUserCycle($uid, $type, $period);
      $from = strtotime($time['start']);
      $to = strtotime($time['end']);
    }
    else {
      $fromto = explode('+', $period);
      $from = strtotime($fromto[0]);
      $to = strtotime($fromto[1]);
    }

    return [
      '#theme' => 'user_report',
      '#scores' => $this->getRecordDetail($uid, $type, $period, $from, $to),
      '#user' => $user,
      '#total' => $this->getTotalScore($uid, $type, $period),
      '#total_study' => $this->getTotalStudy($uid, $type, $period),
      '#total_gain_study' => $this->getSelfStudyGain($uid, $from, $to),
      '#total_lecture' => $this->getTotalLecture($uid, $type, $period),
      '#type' => $type,
      '#from' => $from,
      '#to' => $to,
      '#now' => date('Y-m-d'),
      '#is_cycle' => $this->getUserCycle($uid, $type, $period),
      '#config' => ConfigPages::config('default'),
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
  public function getResultUser($uid, $from, $to) {
    $ids = \Drupal::entityQuery('score')
      ->condition('field_user', $uid)
      ->condition('created', $from, '>=')
      ->condition('created', $to, '<=')
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
    $total = 0;
    $time = $this->getUserCycle($uid, $type, $period);
    $from = strtotime($time['start']);
    $to = strtotime($time['end']);
    if (strpos($period, '1st') !== FALSE) {
      $total = $this->getSelfStudyEveryYear($uid, $from, $to);
    }
    if (strpos($period, '2nd') !== FALSE) {
      $from2 = $from + (365*86400);
      $to1 = $to - (365*86400);
      $total += $this->getSelfStudyEveryYear($uid, $from, $to1);
      $total += $this->getSelfStudyEveryYear($uid, $from2, $to);
    }
    if (strpos($period, '3rd') !== FALSE) {
      $from2 = $from + (365*86400);
      $from3 = $from + (365*86400*2);
      $to1 = $to - (365*86400*2);
      $to2 = $to - (365*86400);
      $total += $this->getSelfStudyEveryYear($uid, $from, $to1);
      $total += $this->getSelfStudyEveryYear($uid, $from2, $to2);
      $total += $this->getSelfStudyEveryYear($uid, $from3, $to);
    }
    return $total;

  }

  /**
   * @param $uid
   * @param $from
   * @param $to
   *
   * @return int
   */
  private function getSelfStudyEveryYear($uid, $from, $to) {
    $scores = $this->getResultUser($uid, $from, $to);
    $total = 0;
    foreach ($scores as $score) {
      if ($score->get('field_score_type')->value == 'Self-Study') {
        $total += $score->get('field_score')->value;
      }
    }
    if ($total > 20) {
      return 20;
    }
    return $total;
  }

  private function getSelfStudyGain($uid, $from, $to) {
    $scores = $this->getResultUser($uid, $from, $to);
    $total = 0;
    foreach ($scores as $score) {
      if ($score->get('field_score_type')->value == 'Self-Study') {
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
  public function getTotalLecture($uid, $type, $period) {

    $time = $this->getUserCycle($uid, $type, $period);
    $from = strtotime($time['start']);
    $to = strtotime($time['end']);
    $scores = $this->getResultUser($uid, $from, $to);
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
  public function getRecordDetail($uid, $type, $period, $from, $to) {
    $data = [];
    $scores = $this->getResultUser($uid, $from, $to);
    foreach ($scores as $score) {
      if ($score->get('field_quiz')->target_id > 0) {
        $quiz = \Drupal\cme_quiz\Entity\Quiz::load($score->get('field_quiz')->target_id);
        $data[] = [
          'date' => $score->get('created')->value,
          'name' => $quiz->get('name')->value,
          'score' => $score->get('field_score')->value,
          'organizer' => $quiz->get('field_organiser')->value,
          'type' => $score->get('field_score_type')->value,
        ];
      }
      elseif ($score->get('field_event')->target_id > 0) {
        $event = \Drupal\cme_event\Entity\CmeEvent::load($score->get('field_event')->target_id);
        $data[] = [
          'date' => $score->get('created')->value,
          'name' => $event->get('name')->value,
          'score' => $score->get('field_score')->value,
          'organizer' => $this->getCategory($event->get('field_organizer')->target_id),
          'type' => $score->get('field_score_type')->value,
        ];
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

    $users = $this->getUsers($type, $period);

    foreach ($users as $uid => $time) {
      $user = \Drupal\user\Entity\User::load($uid);
      if ($type == 'period') {
        $start = strtotime($time['start']);
        $end = strtotime($time['end']);
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


  /**
   * @param $type
   * @param $period
   *
   * @return array
   */
  private function getUsers($type, $period) {
    $users = [];
    $ids = \Drupal::entityQuery('user')->condition('status', 1)->execute();
    $result = \Drupal\user\Entity\User::loadMultiple($ids);
    foreach ($result as $user) {

      if (in_array('hkdu_members', $user->getRoles()) || in_array('doctor', $user->getRoles()) || in_array('cme_member',
          $user->getRoles()) || in_array('council_members', $user->getRoles()) || in_array('drug_suppliers',
          $user->getRoles())) {

        if ($year = getCycle($user)) {
          if (strpos($period, '1st') !== FALSE) {
            if (strpos($user->get('field_cme_join_date')->value, '01-01') !== FALSE && strpos($period,
                '01_01') !== FALSE) {
              $users[$user->id()] = ['start' => $year . '-01-01', 'end' => $year . '-12-31'];
            }
            if (strpos($user->get('field_cme_join_date')->value, '07-01') !== FALSE && strpos($period,
                '01_07') !== FALSE) {
              $users[$user->id()] = ['start' => $year . '-07-01', 'end' => ($year + 1) . '-06-30'];
            }
          }
          if (strpos($period, '2nd') !== FALSE) {
            if (strpos($user->get('field_cme_join_date')->value, '01-01') !== FALSE && strpos($period,
                '01_01') !== FALSE) {
              $users[$user->id()] = ['start' => $year . '-01-01', 'end' => ($year + 1) . '-12-31'];
            }
            if (strpos($user->get('field_cme_join_date')->value, '07-01') !== FALSE && strpos($period,
                '01_07') !== FALSE) {
              $users[$user->id()] = ['start' => $year . '-07-01', 'end' => ($year + 2) . '-06-30'];
            }
          }
          if (strpos($period, '3rd') !== FALSE) {
            if (strpos($user->get('field_cme_join_date')->value, '01-01') !== FALSE && strpos($period,
                '01_01') !== FALSE) {
              $users[$user->id()] = ['start' => $year . '-01-01', 'end' => ($year + 2) . '-12-31'];
            }
            if (strpos($user->get('field_cme_join_date')->value, '07-01') !== FALSE && strpos($period,
                '01_07') !== FALSE) {
              $users[$user->id()] = ['start' => $year . '-07-01', 'end' => ($year + 3) . '-06-30'];
            }
          }

        }
      }
    }
    return $users;
  }

  /**
   * @param $uid
   * @param $type
   * @param $period
   *
   * @return array
   */
  private function getUserCycle($uid, $type, $period) {
    $date = [];
    $user = \Drupal\user\Entity\User::load($uid);
    if ($year = getCycle($user)) {

      if (strpos($period, '1st') !== FALSE) {
        if (strpos($user->get('field_cme_join_date')->value, '01-01') !== FALSE && strpos($period, '01_01') !== FALSE) {
          $date = ['start' => $year . '-01-01', 'end' => $year . '-12-31'];
        }
        if (strpos($user->get('field_cme_join_date')->value, '07-01') !== FALSE && strpos($period, '07_01') !== FALSE) {

          $date = ['start' => $year . '-07-01', 'end' => ($year + 1) . '-06-30'];
        }
      }
      elseif (strpos($period, '2nd') !== FALSE) {
        if (strpos($user->get('field_cme_join_date')->value, '01-01') !== FALSE && strpos($period, '01_01') !== FALSE) {
          $date = ['start' => $year . '-01-01', 'end' => ($year + 1) . '-12-31'];
        }
        if (strpos($user->get('field_cme_join_date')->value, '07-01') !== FALSE && strpos($period, '07_01') !== FALSE) {
          $date = ['start' => $year . '-07-01', 'end' => ($year + 2) . '-06-30'];
        }
      }
      elseif (strpos($period, '3rd') !== FALSE) {
        if (strpos($user->get('field_cme_join_date')->value, '01-01') !== FALSE && strpos($period, '01_01') !== FALSE) {
          $date = ['start' => $year . '-01-01', 'end' => ($year + 2) . '-12-31'];
        }
        if (strpos($user->get('field_cme_join_date')->value, '07-01') !== FALSE && strpos($period, '07_01') !== FALSE) {
          $date = ['start' => $year . '-07-01', 'end' => ($year + 3) . '-06-30'];
        }
      }

    }
    return $date;
  }

}

