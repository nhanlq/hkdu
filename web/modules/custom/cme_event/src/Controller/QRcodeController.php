<?php

namespace Drupal\cme_event\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class QRcodeController.
 */
class QRcodeController extends ControllerBase {

  /**
   * Qrcode.
   *
   * @return string
   *   Return Hello string.
   */
  public function qrcode($event_id, $uid) {

      //update score by Event, User
      $event = \Drupal\cme_event\Entity\CmeEvent::load($event_id);
      $user = \Drupal\user\Entity\User::load($uid);
      if($excore = $this->getUserScoreExist($uid, $event_id)){
          $excore->set('field_score', number_format($event->get('field_cme_point')->value,2));
          $excore->set('field_attendance',1);
          $excore->set('field_date',date('Y-m-d'));
          $excore->save();
          $user->set('field_point',$user->get('field_point')->value + number_format($event->get('field_cme_point')->value,2));
          $user->save();
          return [
              '#type' => 'markup',
              '#markup' => $this->t('Update Score for user success.'),
          ];
      }else{
          if(!$this->getUserExistAttendance($uid, $event->id())){
              $score = \Drupal\cme_score\Entity\Score::create([
                  'name' => 'Event Score of event ' . $event->getName() . ' of User ' . $user->getDisplayName(),
                  'field_score' => number_format($event->get('field_cme_point')->value,2),
                  'field_user' => $user->id(),
                  'field_event' => $event->id(),
                  'field_attendance' => 1,
                  'field_date' => date('Y-m-d'),
                  'uid' => $user->id()
              ]);
              $score->save();
              //set point for user;
              $user->set('field_point',$user->get('field_point')->value + number_format($event->get('field_cme_point')->value,2));
              $user->save();
              return [
                  '#type' => 'markup',
                  '#markup' => $this->t('Update Score for user success.'),
              ];
          }else{
              return [
                  '#type' => 'markup',
                  '#markup' => $this->t('Member: '.$user->getEmail().' ready attendanced this event. Please try again.'),
              ];
          }

      }



  }
    public function getUserScoreExist($uid, $eventId)
    {
        $ids = \Drupal::entityQuery('score')
            ->condition('status', 1)
            ->condition('field_user', $uid)
            ->condition('field_event', $eventId)
            ->condition('field_attendance' ,0)
            ->execute();
        $results = \Drupal\cme_score\Entity\Score::loadMultiple($ids);
        return reset($results);
    }
    public function getUserExistAttendance($uid, $eventId)
    {
        $ids = \Drupal::entityQuery('score')
            ->condition('status', 1)
            ->condition('field_user', $uid)
            ->condition('field_event', $eventId)
            ->condition('field_attendance' ,1)
            ->execute();
        $results = \Drupal\cme_score\Entity\Score::loadMultiple($ids);
        if($results){
            return reset($results);
        }else{
            return false;
        }

    }

}
