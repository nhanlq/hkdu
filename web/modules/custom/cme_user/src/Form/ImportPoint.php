<?php

namespace Drupal\cme_user\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ImportPoint.
 */
class ImportPoint extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'import_point';
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
          '#upload_validators' => array(
              'file_validate_extensions' => array('xls xlsx'),
              // Pass the maximum file size in bytes
          ),
          '#description' => $this->t('Allow Extension: xls, xlsx. Click <a target="_blank" href="/sites/default/files/import/cme_point_example.xlsx">here</a> to download example.')
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
      $stream_wrapper_manager = \Drupal::service('stream_wrapper_manager')->getViaUri($file->getFileUri());
      $file_path = $stream_wrapper_manager->realpath();
      $name = $file->getFilename();
      if(strpos($name, 'xlsx') !==false){
          $inputFileType = 'Xlsx';
      }else{
          $inputFileType = 'Xls';
      }
      /**  Create a new Reader of the type defined in $inputFileType  **/
      $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
      /**  Load $inputFileName to a Spreadsheet Object  **/
      $spreadsheet = $reader->load($file_path);
      $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

      $i = 1;
      foreach( $sheetData as $data){
          if($i != 1){
              if($u = user_load_by_mail($data['A'])){
                  $point = $data['B']; // string
                  $user = \Drupal\user\Entity\User::load($u->id());
                  $user->set('field_point', ($user->get('field_point')->value + $point));
                  try {
                      $user->save();
                      \Drupal::messenger()->addMessage('Add point for '.$data['A'].' success.');
                  }catch (\Exception $e){
                      \Drupal::messenger()->addMessage('Add point for '.$data['A'].' error: '.$e->getMessage(),'error');
                  }
              }
          }
          $i ++;
      }
  }

}
