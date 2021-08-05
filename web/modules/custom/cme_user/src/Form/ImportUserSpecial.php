<?php

namespace Drupal\cme_user\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class ImportUser.
 */
class ImportUserSpecial extends FormBase {

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
      '#description' => $this->t('Allow Extension: xls, xlsx. Click <a target="_blank" href="/sites/default/files/import/MembershipSpecial.xlsx">here</a> to download example.'),
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
      if ($i != 1) {
        if (!empty($data['A'])) {
          if ($user = $this->getUserByMCHKNo($data['A'])) {
            $date = date('Y-m-d', strtotime($data['D']));
            $user->set('field_first_name', $data['B']);
            $user->set('field_cme_join_date', $date);
            $user->set('field_contact_number', $data['E']);
            $user->set('field_address', $data['F']);
            if ($data['C'] == 'Yes') {
              $user->addRole('administrator');
              $user->set('field_hkdu_administrator', 1);
            }else{
              $user->set('field_hkdu_administrator', 0);
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
          else {
            $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
            $user = \Drupal\user\Entity\User::create();
            $date = date('Y-m-d', strtotime($data['D']));

            //Mandatory settings
            $user->setPassword(!empty($data['C']) ? $data['C'] : 'hkdu123');
            $user->enforceIsNew();
            $user->setEmail($data['G']);
            $user->setUsername($data['B']); //This username must be unique
            // and accept only a-Z,0-9, - _ @ .

            //Optional settings
            $user->set("init", $data['G']);
            $user->set("langcode", $language);
            $user->set("preferred_langcode", $language);
            $user->set("preferred_admin_langcode", $language);
            $user->set('field_first_name', $data['B']);
            $user->set('field_cme_join_date', $date);
            $user->set('field_contact_number', $data['E']);
            $user->set('field_address', $data['F']);
            $user->set('field_mchk_license', $data['A']);
            $user->set('status', 0);
            if ($data['C'] == 'yes') {
              $user->addRole('administrator');
            }

            try {
              $user->save();
              \Drupal::messenger()->addMessage('Create member ' . $data['A'] . ' success.');
              $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/admin/members')->toString());
              $redirect->send();
            } catch (\Exception $e) {
              \Drupal::messenger()->addMessage('Create member ' . $data['A'] . ' error ' . $e->getMessage(), 'error');
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
  public function createType($name, $vid) {
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
  public function statusMapping($key) {
    $status = [
      'Blocked' => 0,
      'Active' => 1,
    ];
    return $status[$key];
  }

  /**
   * @param $mchk
   *
   * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface|\Drupal\user\Entity\User|false|null
   */
  public function getUserByMCHKNo($mchk) {
    $ids = \Drupal::entityQuery('user')->condition('field_mchk_license', $mchk)->execute();
    if ($ids) {
      return \Drupal\user\Entity\User::load(reset($ids));
    }
    return FALSE;
  }
}
