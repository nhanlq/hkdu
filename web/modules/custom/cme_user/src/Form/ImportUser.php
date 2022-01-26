<?php

namespace Drupal\cme_user\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class ImportUser.
 */
class ImportUser extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'import_user';
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
      '#description' => $this->t('Allow Extension: xls, xlsx. Click <a target="_blank" href="/sites/default/files/import/MemberShip.xlsx">here</a> to download example.'),
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

    foreach ($sheetData as $data) {
      if ($i > 1) {
        if (!empty($data['A'])) {
          if ($user = user_load_by_mail($data['A'])) {
            $user->setUsername($data['B']);
            $user->set('field_first_name', $data['K']);
            $user->set('field_last_name', $data['L']);
            $user->set('field_contact_number', $data['M']);
            $user->set('field_specialty', $data['N']);
            $user->set('field_registration_no', $data['F']);
            $user->set('field_mchk_license', $data['E']);
            $user->set('field_membership_type', ['target_id'=>$this->createType('membership_type',$data['I'])]);
            $user->set('field_referee', $data['Q'].' '.$data['P']);
            $user->set('field_clinic_id', ['target_id'=>$data['Q']]);
            $user->set('field_specialist_fellow_number', $data['O']);
            $user->set('field_hkdu_subscription_period', $data['J']);
            $userRolesArray = explode(',', $data['H']);//role id
            $user->set('status', $data['M']);
            if ($this->statusMapping($data['D']) == 1) {
              $user->activate();
            }
            foreach ($userRolesArray as $key => $role) {
              $user->addRole($this->mappingRoles(trim($role)));
            }
            try {
              $user->save();

              \Drupal::messenger()
                ->addMessage('Update member ' . $data['A'] . ' success.');
            } catch (\Exception $e) {
              \Drupal::messenger()
                ->addMessage('Update member ' . $data['A'] . ' error ' . $e->getMessage(),
                  'error');
            }

          }
          else {
            $language = \Drupal::languageManager()
              ->getCurrentLanguage()
              ->getId();
            $user = \Drupal\user\Entity\User::create();

            //Mandatory settings
            $user->setPassword(!empty($data['C']) ? $data['C'] : 'hkdu123');
            $user->enforceIsNew();
            $user->setEmail($data['A']);
            $user->setUsername($data['B']); //This username must be unique
            // and accept only a-Z,0-9, - _ @ .

            //Optional settings
            $user->set("init", $data['A']);
            $user->set("langcode", $language);
            $user->set("preferred_langcode", $language);
            $user->set("preferred_admin_langcode", $language);
            $user->set('field_first_name', $data['K']);
            $user->set('field_last_name', $data['L']);
            $user->set('field_contact_number', $data['M']);
            $user->set('field_specialty', $data['N']);
            $user->set('field_registration_no', $data['F']);
            $user->set('field_mchk_license', $data['E']);
            $user->set('field_membership_type', ['target_id'=>$this->createType('membership_type',$data['I'])]);
            $user->set('field_referee', $data['Q'].' '.$data['P']);
            $user->set('field_clinic_id', ['target_id'=>$data['Q']]);
            $user->set('field_specialist_fellow_number', $data['O']);
            $user->set('field_hkdu_subscription_period', $data['J']);
            $user->set('status', $data['D']);
            $userRolesArray = explode(',', $data['H']);//role id
            // $user->set('status',$data['M']);
            foreach ($userRolesArray as $key => $role) {
              $user->addRole($this->mappingRoles(trim($role)));
            }
            //$user->set("setting_name", 'setting_value');
            if ($this->statusMapping($data['D']) == 1) {
              $user->activate();
            }

            try {
              $user->save();
              \Drupal::messenger()
                ->addMessage('Create member ' . $data['A'] . ' success.');
              $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/admin/members')
                ->toString());
              $redirect->send();
            } catch (\Exception $e) {
              \Drupal::messenger()
                ->addMessage('Create member ' . $data['A'] . ' error ' . $e->getMessage(),
                  'error');
            }
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
  public function createType($vid, $name ) {
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
   * @param $key
   *
   * @return int
   */
  public function statusMapping($key){
    $status = [
      'Blocked' => 0,
      'Active' => 1,
    ];
    return $status[$key];
  }
}
