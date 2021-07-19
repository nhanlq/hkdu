<?php

namespace Drupal\cme_user\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class ImportUser.
 */
class ImportUserAdditional extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'import_user_additional';
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
      '#description' => $this->t('Allow Extension: xls, xlsx. Click <a target="_blank" href="/sites/default/files/import/MembershipAdditional.xlsx">here</a> to download example.'),
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
      if (!empty($data['A'])) {
        if ($user = $this->getUserByMCHKNo($data['A'])) {
          $date = date('Y-m-d', strtotime($data['C']));
          $user->set('field_cme_join_date', $date);
          $user->set('field_college', $this->createCollege('cme_college', $data['D']));
          if ($data['B'] == 'Yes') {
            $user->addRole('administrator');
          }
          try {
            $user->save();
            \Drupal::messenger()->addMessage('Update member ' . $data['A'] . ' success.');
            $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/admin/members')->toString());
            $redirect->send();
          } catch (\Exception $e) {
            \Drupal::messenger()->addMessage('Update member ' . $data['A'] . ' error ' . $e->getMessage(), 'error');
          }

        }
      }

      $i++;
    }
  }

  /**
   * @param $role
   *
   * @return string
   */
  public function mappingRoles($role) {
    $roles = [
      'Administrator' => 'administrator',
      'Admins' => 'admins',
      'Doctor' => 'doctor',
      'HKDU Member' => 'hkdu_members',
      'Drug Supplier' => 'drug_suppliers',
      'Council Member' => 'council_members',
      'CME Member' => 'cme_member',
      'Invited' => 'invited',
      'Tester' => 'tester',
    ];
    return $roles[$role];
  }

  /**
   * @param $name
   * @param $vid
   *
   * @return false|int|mixed|string|null
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function createCollege($vid, $name) {
    $data = [];
    if (!empty($name)) {
      $nameArr = explode(',', $name);
      $ids = \Drupal::entityQuery('taxonomy_term')
        ->condition('status', 1)
        ->condition('vid', $vid)
        ->condition('name', $name, 'IN')
        ->execute();
      if ($ids) {
        foreach ($ids as $id => $term) {
          $data[] = [
            'target_id' => $id,
          ];
        }
      }
      else {
        foreach ($nameArr as $na) {
          $term = \Drupal\taxonomy\Entity\Term::create([
            'name' => $na,
            'vid' => $vid,
          ]);
          $term->save();
          $data[] = [
            'target_id' => $term->id(),
          ];
        }
      }
    }

    return $data;
  }

  /**
   * @param $key
   *
   * @return int
   */
  public function statusMapping($key) {
    $status = [
      'Blocked' => 0,
      'Active' => 1,
    ];
    return $status[$key];
  }

  public function getUserByMCHKNo($mchk) {
    $ids = \Drupal::entityQuery('user')->condition('field_mchk_license', $mchk)->execute();
    if ($ids) {
      return \Drupal\user\Entity\User::load(reset($ids));
    }
    return FALSE;
  }
}
