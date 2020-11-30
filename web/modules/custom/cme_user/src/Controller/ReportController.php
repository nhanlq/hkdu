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
  public function report($uid, $from, $to) {
    $user = \Drupal\user\Entity\User::load($uid);
    return [
      '#theme' => 'user_report',
      '#scores' => $this->getRecordDetail($uid, $from, $to),
      '#user' => $user,
      '#total' => $this->getTotalScore($uid, $from, $to),
      '#total_study' =>$this->getTotalStudy($uid, $from, $to),
      '#total_lecture' => $this->getTotalLecture($uid, $from, $to),
      '#from' => $from,
      '#to' =>$to,
      '#now' => date('Y-m-d')
    ];
  }

  public function getResultUser($uid, $from, $to){
    $from_date = new DrupalDateTime($from);
    $from_date->setTimezone(new \DateTimezone(DATETIME_STORAGE_TIMEZONE));
    $from_formatted = $from_date->format(DATETIME_DATETIME_STORAGE_FORMAT);

    $to_date = new DrupalDateTime($to);
    $to_date->setTimezone(new \DateTimezone(DATETIME_STORAGE_TIMEZONE));
    $to_formatted = $to_date->format(DATETIME_DATETIME_STORAGE_FORMAT);

    $ids = \Drupal::entityQuery('score')
      ->condition('status', 1)
      ->condition('field_attendance',1)
      ->condition('field_user',$uid)
      ->condition('field_date',$from_formatted,'>=')
      ->condition('field_date',$to_formatted,'<=')
      ->execute();
    $result = \Drupal\cme_score\Entity\Score::loadMultiple($ids);
    return $result;
  }

  public function getTotalStudy($uid, $from, $to){
    $scores = $this->getResultUser($uid, $from, $to);
    $total = 0;
    foreach($scores as $score){
      if($score->get('field_quiz')->target_id > 0){
         $quiz = \Drupal\cme_quiz\Entity\Quiz::load($score->get
         ('field_quiz')->target_id);
         if($quiz->get('field_category')->target_id == 53){
           $total += $score->get('field_score')->value;
         }
      }
      if($score->get('field_event')->target_id > 0){
          $event = \Drupal\cme_event\Entity\CmeEvent::load($score->get
          ('field_event')->target_id);
        if($event->get('field_category')->target_id == 53){
          $total += $score->get('field_score')->value;
        }
      }
      if($score->get('field_epharm_event')->target_id > 0){
        $event = \Drupal\event\Entity\Event::load($score->get
        ('field_epharm_event')->target_id);
        if($event->get('field_category')->target_id == 53){
          $total += $score->get('field_score')->value;
        }
      }
    }
    return $total;
  }

  public function getTotalLecture($uid, $from, $to){
    $scores = $this->getResultUser($uid, $from, $to);
    $total = 0;
    foreach($scores as $score){
      if($score->get('field_quiz')->target_id > 0){
        $quiz = \Drupal\cme_quiz\Entity\Quiz::load($score->get
        ('field_quiz')->target_id);
        if($quiz->get('field_category')->target_id == 104){
          $total += $score->get('field_score')->value;
        }
      }
      if($score->get('field_event')->target_id > 0){
        $event = \Drupal\cme_event\Entity\CmeEvent::load($score->get
        ('field_event')->target_id);
        if($event->get('field_category')->target_id == 104){
          $total += $score->get('field_score')->value;
        }
      }
      if($score->get('field_epharm_event')->target_id > 0){
        $event = \Drupal\event\Entity\Event::load($score->get
        ('field_epharm_event')->target_id);
        if($event->get('field_category')->target_id == 104){
          $total += $score->get('field_score')->value;
        }
      }
    }
    return $total;
  }

  public function getTotalScore($uid, $from, $to){
    $scores = $this->getResultUser($uid, $from, $to);

    $total = 0;
    foreach($scores as $score){
      $total += $score->get('field_score')->value;
    }
    return $total;
  }
  public function getRecordDetail($uid, $from, $to){
    $data = [];
    $scores = $this->getResultUser($uid, $from, $to);
    foreach($scores as $score){

      if($score->get('field_quiz')->target_id > 0){
        $quiz = \Drupal\cme_quiz\Entity\Quiz::load($score->get
        ('field_quiz')->target_id);
        $data[] = ['date'=> $score->get('field_date')->value,
          'name'=>$quiz->get('name')->value,'score' => $score->get('field_score')->value,'organizer' =>$score->get('field_organizer')->value];
      }elseif ($score->get('field_event')->target_id > 0){
        $event = \Drupal\cme_event\Entity\CmeEvent::load($score->get
        ('field_event')->target_id);
        $data[] = ['date'=> $score->get('field_date')->value,
          'name'=>$event->get('name')->value,'score' => $score->get('field_score')->value,'organizer' =>$score->get('field_organizer')->value];
      }else{
        $event = \Drupal\event\Entity\Event::load($score->get
        ('field_epharm_event')->target_id);
        $data[] = ['date'=> $score->get('field_date')->value,
          'name'=>$event->get('name')->value,'score' => $score->get('field_score')->value,'organizer' =>$score->get('field_organizer')->value];
      }

    }
    return $data;
  }
}
