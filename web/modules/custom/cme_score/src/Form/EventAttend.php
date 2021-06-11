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
    $form['excel'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Excel'),
      '#weight' => '0',
      '#upload_location' => 'public://import/',
      '#upload_validators' => [
        'file_validate_extensions' => ['xls xlsx'],
        // Pass the maximum file size in bytes
      ],
      '#description' => $this->t('Allow Extension: xls, xlsx. Click <a target="_blank" href="/sites/default/files/import/cme_event_attend.xlsx">here</a> to download example.'),
      '#weight' => 2,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#weight' => 3,
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
    $stream_wrapper_manager = \Drupal::service('stream_wrapper_manager')->getViaUri($file->getFileUri());
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
    $sheetData = $spreadsheet->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);
    $i = 1;

    foreach ($sheetData as $data) {

      $attended = 0;
      if ($i != 1) {
        if ($data['C'] != 'NO') {
          if ($cme_event = $this->getEvents($data['B'])) {
            if ($user = $this->getUserByReno($data['A'])) {
              if (!$this->getUserScoreExist($user->id(), $cme_event->id())) {
                $admin = 1;
                if ($data['D'] == 'No') {
                  $admin = 0;
                }
                $score = \Drupal\cme_score\Entity\Score::create([
                  'name' => 'Event Score of event ' . $cme_event->getName() . ' of User ' . $user->getDisplayName(),
                  'field_score' => $cme_event->get('field_cme_point')->value,
                  'field_user' => $user->id(),
                  'field_event' => $cme_event->id(),
                  'field_hkdu_administrator' => $admin,
                  'field_score_type' => $cme_event->get('field_type')->value,
                  'uid' => $user->id(),
                  'created' => time(),
                  'changed' => time(),
                ]);
                $score->save();
                //set score for user
                $point = $cme_event->get('field_cme_point')->value;
                if ($cme_event->get('field_type')->value == 'Lecture') {
                  if ($user->get('field_lecture_point')->value < 20) {
                    if ($user->get('field_lecture_point')->value + $point <= 20) {
                      $user->set('field_lecture_point', $user->get('field_lecture_point')->value + $point);
                      $user->set('field_cme_point', $user->get('field_cme_point')->value + $point);
                    }
                    else {
                      $user->set('field_lecture_point', 20);
                      $user->set('field_cme_point',
                        $user->get('field_cme_point')->value + (20 - $user->get('field_lecture_point')->value));
                    }
                  }
                }
                if ($cme_event->get('field_type')->value == 'Self-Study') {
                  $user->set('field_self_study_point', $user->get('field_self_study_point')->value + $point);
                  $user->set('field_cme_point', $user->get('field_cme_point')->value + $point);
                }
                $user->save();

                \Drupal::messenger()
                  ->addMessage('User ' . $data['A'] . ' attended the event  ' . $data['B'] . ' success. ', 'error');
              }
            }
            else {
              \Drupal::messenger()->addMessage('Member ' . $data['A'] . ' does not exist. ', 'error');
            }

          }
          else {
            \Drupal::messenger()->addMessage('Event ' . $data['B'] . ' does not exist. ', 'error');
          }
        }

      }
      $i++;
    }

    \Drupal::messenger()->addMessage('Import Attendance success.');

  }

  public function getEvents($ref) {
    $ids = \Drupal::entityQuery('cme_event')->condition('field_ref_code', $ref)->sort('name', 'ASC')->execute();
    if ($ids) {
      $results = \Drupal\cme_event\Entity\CmeEvent::loadMultiple($ids);
      return reset($results);
    }
    return FALSE;
  }


  /**
   * @param $mchk
   *
   * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface|\Drupal\user\Entity\User|false
   */
  public function getUserByReno($mchk) {
    $ids = \Drupal::entityQuery('user')->condition('field_mchk_license', $mchk)->execute();
    if ($ids) {
      $results = \Drupal\user\Entity\User::loadMultiple($ids);
      if ($results) {
        return reset($results);
      }
    }
    return FALSE;

  }

  /**
   * @param $uid
   * @param $eventId
   *
   * @return bool
   */
  public function getUserScoreExist($uid, $eventId) {
    $ids = \Drupal::entityQuery('score')
      ->condition('status', 1)
      ->condition('field_user', $uid)
      ->condition('field_event', $eventId)
      ->execute();
    $results = \Drupal\cme_score\Entity\Score::loadMultiple($ids);
    if ($results) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

}
