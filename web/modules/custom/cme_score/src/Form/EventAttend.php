<?php

namespace Drupal\cme_score\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EventAttend.
 */
class EventAttend extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'event_attend';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['event'] = [
            '#type' => 'select',
            '#title' => 'Event',
            '#options' => $this->getEvents(),
            '#required' => true,
        ];
        $form['excel'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Excel'),
            '#weight' => '0',
            '#upload_location' => 'public://import/',
            '#upload_validators' => array(
                'file_validate_extensions' => array('xls xlsx'),
                // Pass the maximum file size in bytes
            ),
            '#description' => $this->t('Allow Extension: xls, xlsx. Click <a target="_blank" href="/sites/default/files/import/cme_attend.xlsx">here</a> to download example.')
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
        ];


        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        foreach ($form_state->getValues() as $key => $value) {
            // @TODO: Validate fields.
        }
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // Display result.
        // Display result.
        $file = \Drupal\file\Entity\File::load($form_state->getValue('excel')[0]);
        $inputFileName = file_create_url($file->getFileUri());
        $stream_wrapper_manager = \Drupal::service('stream_wrapper_manager')->getViaUri($file->getFileUri());
        $file_path = $stream_wrapper_manager->realpath();
        $name = $file->getFilename();
        if (strpos($name, 'xlsx') !== false) {
            $inputFileType = 'Xlsx';
        } else {
            $inputFileType = 'Xls';
        }
        /**  Create a new Reader of the type defined in $inputFileType  **/
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        /**  Load $inputFileName to a Spreadsheet Object  **/
        $spreadsheet = $reader->load($file_path);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $i = 1;
        $event = \Drupal\cme_event\Entity\CmeEvent::load($form_state->getValue('event'));

        foreach ($sheetData as $data) {
            $attended = 0;
            if ($i != 1) {

                // var_dump($data['C']);die;
                if ($data['D'] == 'yes') {
                    $attended = 1;
                }
                if (!empty($data['B']) && $user = $this->getUserByReno($data['B'])) {
                    if (!$this->getUserScoreExist($user->id(), $event->id())) {
                        $score = \Drupal\cme_score\Entity\Score::create([
                            'name' => 'Event Score of event ' . $event->getName() . ' of User ' . $user->getDisplayName(),
                            'field_score' => number_format($data['C'],2),
                            'field_user' => $user->id(),
                            'field_event' => $event->id(),
                            'field_attendance' => $attended,
                            'field_accreditor' =>$data['E'],
                            'uid' => $user->id()
                        ]);
                        $score->save();
                        //set score for user
                        $user->set('field_point',$user->get('field_point')->value + number_format($data['C'],2));
                        $user->save();
                    }

                } else {
                    if(!empty($data['A'])){
                        if ($user = user_load_by_mail($data['A'])) {
                            if (!$this->getUserScoreExist($user->id(), $event->id())) {
                                $score = \Drupal\cme_score\Entity\Score::create([
                                    'name' => 'Event Score of event ' . $event->getName() . ' of User ' . $user->getDisplayName(),
                                    'field_score' => number_format($data['C'],2),
                                    'field_user' => $user->id(),
                                    'field_event' => $event->id(),
                                    'field_attendance' => $attended,
                                    'field_accreditor' =>$data['E'],
                                    'uid' => $user->id()
                                ]);
                                $score->save();
                                //set point for user;
                                $user->set('field_point',$user->get('field_point')->value + number_format($data['C'],2));
                                $user->save();
                            }
                        }
                    }

                }

            }
            $i++;
        }
    }

    public function getEvents()
    {
        $ids = \Drupal::entityQuery('cme_event')
            ->condition('status', 1)
            ->sort('name', 'ASC')
            ->execute();
        $results = \Drupal\cme_event\Entity\CmeEvent::loadMultiple($ids);
        $events = [];
        foreach ($results as $result) {
            $events[$result->id()] = $result->getName();
        }
        return $events;
    }

    public function getUserByReno($id)
    {
        $ids = \Drupal::entityQuery('user')
            ->condition('status', 1)
            ->condition('field_registration_no', $id)
            ->execute();
        $results = \Drupal\user\Entity\User::loadMultiple($ids);
        if ($results) {
            return reset($results);
        } else {
            return false;
        }
    }

    public function getUserScoreExist($uid, $eventId)
    {
        $ids = \Drupal::entityQuery('score')
            ->condition('status', 1)
            ->condition('field_user', $uid)
            ->condition('field_event', $eventId)
            ->execute();
        $results = \Drupal\cme_score\Entity\Score::loadMultiple($ids);
        if ($results) {
            return true;
        } else {
            return false;
        }
    }

}
