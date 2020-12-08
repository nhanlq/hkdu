<?php

namespace Drupal\cme_score\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EventAttend.
 */
class EventAttend extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'event_attend';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['cmeevent'] = [
      '#type' => 'select',
      '#title' => 'CME Event',
      '#options' => $this->getEvents(),
      '#weight' => 0,
      '#required' => true
    ];
//    $form['epharm_event'] = [
//      '#type' => 'select',
//      '#title' => 'Epharm Event',
//      '#options' => $this->getEpharmEvents(),
//      '#weight' =>1
//    ];
    $form['excel'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Excel'),
      '#weight' => '0',
      '#upload_location' => 'public://import/',
      '#upload_validators' => [
        'file_validate_extensions' => ['xls xlsx'],
        // Pass the maximum file size in bytes
      ],
      '#description' => $this->t('Allow Extension: xls, xlsx. Click <a target="_blank" href="/sites/default/files/import/cme_attend.xlsx">here</a> to download example.'),
      '#weight' =>2
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#weight' => 3
    ];


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    // Display result.
    $file = \Drupal\file\Entity\File::load($form_state->getValue('excel')[0]);
    $inputFileName = file_create_url($file->getFileUri());
    $stream_wrapper_manager = \Drupal::service('stream_wrapper_manager')
      ->getViaUri($file->getFileUri());
    $file_path = $stream_wrapper_manager->realpath();
    $name = $file->getFilename();
    if (strpos($name, 'xlsx') !== FALSE) {
      $inputFileType = 'Xlsx';
    }
    else {
      $inputFileType = 'Xls';
    }
    /**  Create a new Reader of the type defined in $inputFileType  **/
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
    /**  Load $inputFileName to a Spreadsheet Object  **/
    $spreadsheet = $reader->load($file_path);
    $sheetData = $spreadsheet->getActiveSheet()
      ->toArray(NULL, TRUE, TRUE, TRUE);
    $i = 1;
    $cme_event = FALSE;
    if ($form_state->getValue('cmeevent') > 0) {
      $cme_event = \Drupal\cme_event\Entity\CmeEvent::load($form_state->getValue('cmeevent'));
    }
    $epharm_event = FALSE;

    if ($form_state->getValue('epharm_event') > 0) {
      $epharm_event = \Drupal\event\Entity\Event::load($form_state->getValue('epharm_event'));
    }
    foreach ($sheetData as $data) {

      $attended = 0;
      if ($i != 1) {
        // var_dump($data['C']);die;
        if ($data['O'] == 'yes') {
          $attended = 1;
        }
        if($cme_event){
          if (!empty($data['B']) && $user = $this->getUserByReno($data['B'])) {
            if (!$this->getUserScoreExist($user->id(), $cme_event->id(),
              'cme')) {
              $score = \Drupal\cme_score\Entity\Score::create([
                'name' => 'Event Score of event ' . $cme_event->getName() . ' of User ' . $user->getDisplayName(),
                'field_score' => number_format($data['H'], 2),
                'field_user' => $user->id(),
                'field_event' => $cme_event->id(),
                'field_attendance' => $attended,
                'field_organizer' => $data['J'],
                'field_accreditor' => $data['I'],
                'field_date' => date('Y-m-d',strtotime($data['K'])),
                'field_time' => $data['L'],
                'field_venue' => $data['M'],
                'field_speaker' => $data['N'],
                'uid' => $user->id(),
              ]);
              $score->save();
              //set score for user
              $user->set('field_point',
                $user->get('field_point')->value + number_format($data['C'], 2));
              $user->save();
            }

          }
          else {

            if (!empty($data['A'])) {

              if ($user = $this->getUserByReno(null,$data['A'])) {

                if (!$this->getUserScoreExist($user->id(), $cme_event->id(),'cme')) {
                  $score = \Drupal\cme_score\Entity\Score::create([
                    'name' => 'Event Score of event ' . $cme_event->getName() . ' of User ' . $user->getDisplayName(),
                    'field_score' => number_format($data['H'], 2),
                    'field_user' => $user->id(),
                    'field_event' => $cme_event->id(),
                    'field_attendance' => $attended,
                    'field_accreditor' => $data['I'],
                    'field_organizer' => $data['J'],
                    'field_date' =>date('Y-m-d',strtotime($data['K'])),
                    'field_time' => $data['L'],
                    'field_venue' => $data['M'],
                    'field_speaker' => $data['N'],
                    'uid' => $user->id(),
                  ]);
                  $score->save();
                  //set point for user;
                  $user->set('field_point',
                    $user->get('field_point')->value + number_format($data['C'],
                      2));
                  $user->save();
                }
              }
              else {
                $user = $this->create_user($data);
                $score = \Drupal\cme_score\Entity\Score::create([
                  'name' => 'Event Score of event ' . $cme_event->getName() . ' of User ' . $user->getDisplayName(),
                  'field_score' => number_format($data['H'], 2),
                  'field_user' => $user->id(),
                  'field_event' => $cme_event->id(),
                  'field_attendance' => $attended,
                  'field_accreditor' => $data['I'],
                  'field_organizer' => $data['J'],
                  'field_date' => date('Y-m-d',strtotime($data['K'])),
                  'field_time' => $data['L'],
                  'field_venue' => $data['M'],
                  'field_speaker' => $data['N'],
                  'uid' => $user->id(),
                ]);
                $score->save();
              }
            }

          }
        }
        if($epharm_event){
          if (!empty($data['B']) && $user = $this->getUserByReno($data['B'])) {
            if (!$this->getUserScoreExist($user->id(), $epharm_event->id())) {
              $score = \Drupal\cme_score\Entity\Score::create([
                'name' => 'Event Score of event ' . $epharm_event->getName() . ' of User ' . $user->getDisplayName(),
                'field_score' => number_format($data['H'], 2),
                'field_user' => $user->id(),
                'field_epharm_event' => $epharm_event->id(),
                'field_attendance' => $attended,
                'field_accreditor' => $data['I'],
                'field_organizer' => $data['J'],
                'field_date' => date('Y-m-d',strtotime($data['K'])),
                'field_time' => $data['L'],
                'field_venue' => $data['M'],
                'field_speaker' => $data['N'],
                'uid' => $user->id(),
              ]);
              $score->save();
              //set score for user
              $user->set('field_point',
                $user->get('field_point')->value + number_format($data['C'], 2));
              $user->save();
            }

          }
          else {
            if (!empty($data['A'])) {

              if ($user = $this->getUserByReno(null,$data['A'])) {

                if (!$this->getUserScoreExist($user->id(), $epharm_event->id())) {
                  $score = \Drupal\cme_score\Entity\Score::create([
                    'name' => 'Event Score of event ' . $epharm_event->getName() . ' of User ' . $user->getDisplayName(),
                    'field_score' => number_format($data['H'], 2),
                    'field_user' => $user->id(),
                    'field_epharm_event' => $epharm_event->id(),
                    'field_attendance' => $attended,
                    'field_accreditor' => $data['I'],
                    'field_organizer' => $data['J'],
                    'field_date' => date('Y-m-d',strtotime($data['K'])),
                    'field_time' => $data['L'],
                    'field_venue' => $data['M'],
                    'field_speaker' => $data['N'],
                    'uid' => $user->id(),
                  ]);
                  $score->save();
                  //set point for user;
                  $user->set('field_point',
                    $user->get('field_point')->value + number_format($data['C'],
                      2));
                  $user->save();
                }
              }
              else {
                $user = $this->create_user($data);
                $score = \Drupal\cme_score\Entity\Score::create([
                  'name' => 'Event Score of event ' . $cme_event->getName() . ' of User ' . $user->getDisplayName(),
                  'field_score' => number_format($data['H'], 2),
                  'field_user' => $user->id(),
                  'field_event' => $cme_event->id(),
                  'field_attendance' => $attended,
                  'field_accreditor' => $data['I'],
                  'field_organizer' => $data['J'],
                  'field_date' => date('Y-m-d',strtotime($data['K'])),
                  'field_time' => $data['L'],
                  'field_venue' => $data['M'],
                  'field_speaker' => $data['N'],
                  'uid' => $user->id(),
                ]);
                $score->save();
              }
            }

          }
        }


      }
      $i++;
    }

    \Drupal::messenger()
      ->addMessage('Import Attendance success.');

  }

  public function getEvents() {
    $ids = \Drupal::entityQuery('cme_event')
      ->condition('status', 1)
      ->sort('name', 'ASC')
      ->execute();
    $results = \Drupal\cme_event\Entity\CmeEvent::loadMultiple($ids);
    $events = [];
    $events[''] = 'Choose an event';
    foreach ($results as $result) {
      $events[$result->id()] = $result->getName();
    }
    return $events;
  }

  public function getEpharmEvents() {
    $ids = \Drupal::entityQuery('event')
      ->condition('status', 1)
      ->sort('name', 'ASC')
      ->execute();
    $results = \Drupal\event\Entity\Event::loadMultiple($ids);
    $events = [];
    $events[''] = 'Choose an event';
    foreach ($results as $result) {
      $events[$result->id()] = $result->getName();
    }
    return $events;
  }

  public function getUserByReno($id=null, $email=null) {
    if($id){
      $ids = \Drupal::entityQuery('user')
        ->condition('status', 1)
        ->condition('field_registration_no', $id)
        ->execute();
    }
    if($email){
      $ids = \Drupal::entityQuery('user')
        ->condition('status', 1)
        ->condition('mail', $email)
        ->execute();
    }

    $results = \Drupal\user\Entity\User::loadMultiple($ids);
    if ($results) {
      return reset($results);
    }
    else {
      return FALSE;
    }
  }

  public function getUserScoreExist($uid, $eventId, $type = null) {
    if($type=='cme'){
      $ids = \Drupal::entityQuery('score')
        ->condition('status', 1)
        ->condition('field_user', $uid)
        ->condition('field_event', $eventId)
        ->execute();
      $results = \Drupal\cme_score\Entity\Score::loadMultiple($ids);
    }else{
      $ids = \Drupal::entityQuery('score')
        ->condition('status', 1)
        ->condition('field_user', $uid)
        ->condition('field_epharm_event', $eventId)
        ->execute();
      $results = \Drupal\cme_score\Entity\Score::loadMultiple($ids);
    }

    if ($results) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  public function create_user($data) {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $user = \Drupal\user\Entity\User::create();

    //Mandatory settings
    $user->setPassword('hkdu123');
    $user->enforceIsNew();
    $user->setEmail($data['A']);
    $user->setUsername($data['A']); //This username must be unique and accept only a-Z,0-9, - _ @ .

    //Optional settings
    $user->set("init", $data['A']);
    $user->set("langcode", $language);
    $user->set("preferred_langcode", $language);
    $user->set("preferred_admin_langcode", $language);
    $user->set('field_first_name', $data['D']);
    $user->set('field_last_name', $data['E']);
    $user->set('field_point', $data['H']);
    $user->set('field_registration_no', $data['B']);
    $user->set('field_mchk_license', $data['C']);
    $user->set('field_membership_type', $data['G']);
    $user->set('field_referee', $data['F']);
    $user->activate();

    try {
      $user->save();
      return $user;
      \Drupal::messenger()
        ->addMessage('Create member ' . $data['A'] . ' success.');
    } catch (\Exception $e) {
      \Drupal::messenger()
        ->addMessage('Create member ' . $data['A'] . ' error ' . $e->getMessage(),
          'error');
      return FALSE;
    }
  }

}
