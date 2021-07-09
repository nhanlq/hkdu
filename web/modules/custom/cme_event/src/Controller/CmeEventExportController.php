<?php

namespace Drupal\cme_event\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class CmeEventExportController.
 *
 *  Returns responses for CME Event routes.
 */
class CmeEventExportController extends ControllerBase {

  /**
   * Implement List Event Enrollment List
   */
  public function enroll_list($id) {
    $event = \Drupal\cme_event\Entity\CmeEvent::load($id);
    $users = $this->getScoreCme($id);
    foreach ($users as $user) {
      $roles = $user->getRoles();
      $user->role = $this->mappingRoles($roles[1]);
    }
    return [
      '#theme' => 'event_enrollment_list',
      '#users' => $users,
      '#event' => $event,
      '#college' => implode(',', $this->getCollege($event)),
      '#category' => implode(',', $this->getCategory($event)),
      '#special_point' => implode(',', $this->getSpecialPoint($event)),
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  /**
   * @param $id
   *
   * @return \Drupal\cme_score\Entity\Score[]|\Drupal\Core\Entity\EntityBase[]|\Drupal\Core\Entity\EntityInterface[]
   */
  public function getScoreCme($id) {
    $ids = \Drupal::entityQuery('score')->condition('field_event', $id)->execute();
    $scores = \Drupal\cme_score\Entity\Score::loadMultiple($ids);
    $users = [];
    if ($scores) {
      foreach ($scores as $score) {
        $users[$score->get('field_user')->target_id] = \Drupal\user\Entity\User::load($score->get('field_user')->target_id);
      }
    }
    return $users;
  }

  /**
   * @param $event
   */
  public function getCollege($event) {
    $data = [];
    /** @var \Drupal\cme_event\Entity\CmeEvent $event */
    if ($event->get('field_college')) {
      foreach ($event->get('field_college')->getValue() as $col) {
        /** @var \Drupal\paragraphs\Entity\Paragraph $para */
        $para = \Drupal\paragraphs\Entity\Paragraph::load($col['target_id']);
        $college = \Drupal\taxonomy\Entity\Term::load($para->get('field_college')->target_id);
        if($college){
          $data[] = $college->get('name')->value;
        }
      }
    }

    return $data;
  }

  /**
   * @param $event
   */
  public function getCategory($event) {
    $data = [];
    if ($event->get('field_college')) {
      /** @var \Drupal\cme_event\Entity\CmeEvent $event */
      foreach ($event->get('field_college')->getValue() as $col) {
        /** @var \Drupal\paragraphs\Entity\Paragraph $para */
        $para = \Drupal\paragraphs\Entity\Paragraph::load($col['target_id']);
        $college = \Drupal\taxonomy\Entity\Term::load($para->get('field_category')->target_id);
        if($college){
          $data[] = $college->get('name')->value;
        }

      }
    }

    return $data;
  }

  /**
   * @param $event
   */
  public function getSpecialPoint($event) {
    $data = [];
    if ($event->get('field_college')) {
      /** @var \Drupal\cme_event\Entity\CmeEvent $event */
      foreach ($event->get('field_college')->getValue() as $col) {
        /** @var \Drupal\paragraphs\Entity\Paragraph $para */
        $para = \Drupal\paragraphs\Entity\Paragraph::load($col['target_id']);
        if($para){
          $data[] = $para->get('field_special_point')->value;
        }

      }
    }
    return $data;
  }

  public function mappingRoles($role) {
    $roles = [
      'administrator' => 'Administrator',
      'admins' => 'Admins',
      'doctor' => 'Doctor',
      'hkdu_members' => 'HKDU Member',
      'drug_suppliers' => 'Drug Supplier' ,
      'council_members' => 'Council Member',
      'cme_member' => 'CME Member',
      'invited' => 'Invited',
      'tester' => 'Tester',
    ];
    return $roles[$role];
  }

  public function enroll_pending(){
    $scores = $this->getScoreCmePending();
    foreach($scores as $score){
      if($score->get('status')->value == 0){
        $score->status = 'Pending';
      }else{
        $score->status = 'Completed';
      }
    }
    return [
      '#theme' => 'event_enrollment_pending_list',
      '#scores' => $scores,
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  /**
   * @param $id
   *
   * @return \Drupal\cme_score\Entity\Score[]|\Drupal\Core\Entity\EntityBase[]|\Drupal\Core\Entity\EntityInterface[]
   */
  public function getScoreCmePending() {
    $ids = \Drupal::entityQuery('score')
      ->condition('status',0)
      ->execute();
    $scores = \Drupal\cme_score\Entity\Score::loadMultiple($ids);

    return $scores;
  }
}
