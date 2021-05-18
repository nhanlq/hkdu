<?php

namespace Drupal\cme_event\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class ImportEvent.
 */
class ImportEvent extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'import_event';
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
      '#description' => $this->t('Allow Extension: xls, xlsx. Click <a target="_blank" href="/sites/default/files/import/ImportCMEevent.xlsx">here</a> to download example.'),
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
    $user = \Drupal::currentUser();
    foreach ($sheetData as $data) {
      if ($i != 1) {
        if (!$this->checkCmeEvent($data['A'])) {
          $publish_date = date('Y-m-d');
          $start_date = date('Y-m-d', strtotime($data['F']));
          $end_date = date('Y-m-d', strtotime($data['F']));
          $expired = date('Y-m-d', strtotime($data['F']));
          $expired = $expired . 'T' . $data['H'] . ':00';
          $event = \Drupal\cme_event\Entity\CmeEvent::create([
            'name' => $data['B'],
            'uid' => $user->id(),
            'field_ref_code' => $data['A'],
            'field_cme_point' => $data['C'],
            'field_published_date' => $publish_date,
            'field_type' => $data['D'],
            'field_organizer' => [
              'target_id' => $this->getTerm($data['E'], 'cme_organizer'),
            ],
            'field_start_date' => $start_date,
            'field_date' => $end_date,
            'field_expired' => $expired,
            'field_start_time' => $data['G'],
            'field_end_time' => $data['H'],
            'field_veune' => $data['I'],
            'field_speaker' => $data['J'],
            'field_moderator' => $data['K'],
            'field_co_organizer' => $data['L'],
            'field_remark' => $data['M'],
            'field_application_no' => $data['N'],
            'field_weight' => 0,
            'field_college' => $this->getCollege($data),
          ]);
          try {
            $event->save();

            \Drupal::messenger()->addMessage('Add ' . $data['A'] . ' success.');
            $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/admin/cme/cme-event')
              ->toString());
            $redirect->send();
          } catch (\Exception $e) {
            \Drupal::messenger()
              ->addMessage('Add ' . $data['A'] . ' error: ' . $e->getMessage(),
                'error');
          }
        }else{
          \Drupal::messenger()->addMessage('The Event ' . $data['A'] . ' ready exist.','error');
        }
      }
      $i++;
    }
  }

  /**
   * @param $name
   * @param $vid
   *
   * @return false|int|mixed|string|null
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function getTerm($name, $vid) {
    $ids = \Drupal::entityQuery('taxonomy_term')
      ->condition('status', 1)
      ->condition('vid', $vid)
      ->condition('name', $name)
      ->execute();
    if ($ids) {
      $r = reset($ids);
      return $r;
    }
    else {
      $term = \Drupal\taxonomy\Entity\Term::create([
        'name' => $name,
        'vid' => $vid,
      ]);
      $term->save();
      return $term->id();
    }
  }

  /**
   * @param $data
   *
   * @return array
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function getCollege($data) {
    $college = [];
    if (!empty($data['O']) && !empty($data['P']) && !empty($data['Q'])) {
      $cols = explode(';', $data['O']);
      $cats = explode(';', $data['P']);
      $specs = explode(';', $data['Q']);
      foreach ($cols as $key => $col) {
        $para = \Drupal\paragraphs\Entity\Paragraph::create([
            'type' => 'cme_college',
            'field_college' => $this->getTerm($col, 'cme_college'),
            'field_category' => $this->getTerm($cats[$key], 'cme'),
            'field_special_point' => $specs[$key],
          ]);
        $para->save();
        $college[] = [
          'target_id' => $para->id(),
          'target_revision_id' => $para->getRevisionId(),
        ];
      }
    }
    return $college;
  }

  public function checkCmeEvent($ref) {
    $ids = \Drupal::entityQuery('cme_event')
      ->condition('field_ref_code', $ref)
      ->execute();
    if ($ids) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
}
