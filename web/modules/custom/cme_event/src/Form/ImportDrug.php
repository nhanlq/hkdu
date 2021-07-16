<?php

namespace Drupal\cme_event\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class ImportEvent.
 */
class ImportDrug extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'import_drug';
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
      '#description' => $this->t('Allow Extension: xls, xlsx. Click <a target="_blank" href="/sites/default/files/import/drug_database.xlsx">here</a> to download example.'),
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
    $user = \Drupal::currentUser();
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
    $status = 1;
    if(in_array('drug_suppliers', $user->getRoles())){
      $status = 0;
    }
    foreach ($sheetData as $data) {
      if ($i != 1) {
          $node = \Drupal\node\Entity\Node::create([
            'type' =>'know',
            'title' => $data['A'],
            'field_common_name' => $data['B'],
            'field_company' => $data['C'],
            'field_company_tel' => $data['D'],
            'field_company_fax' => $data['E'],
            'field_packing' => $data['F'],
            'field_total_price' => $data['G'],
            'field_remark' => $data['H'],
            'status' => $status,
          ]);
        $node->enforceIsNew();
          try {
            $node->save();

            \Drupal::messenger()->addMessage('Add ' . $data['A'] . ' success.');
            if(in_array('drug_suppliers', $user->getRoles())){
              $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/admin/drug-databases')
                ->toString());
            }else{
              $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/admin/member/content?title=&type=know')
                ->toString());
            }

            $redirect->send();
          } catch (\Exception $e) {
            \Drupal::messenger()
              ->addMessage('Add ' . $data['A'] . ' error: ' . $e->getMessage(),
                'error');
          }
        }
      $i++;
    }
  }
}
