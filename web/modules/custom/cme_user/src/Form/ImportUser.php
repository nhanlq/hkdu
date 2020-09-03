<?php

namespace Drupal\cme_user\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ImportUser.
 */
class ImportUser extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'import_user';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['excel'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Excel'),
            '#weight' => '0',
            '#upload_location' => 'public://import/',
            '#upload_validators' => array(
                'file_validate_extensions' => array('xls xlsx'),
                // Pass the maximum file size in bytes
            ),
            '#description' => $this->t('Allow Extension: xls, xlsx. Click <a target="_blank" href="/sites/default/files/import/cme_user.xlsx">here</a> to download example.')
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

        foreach ($sheetData as $data) {
            if ($i != 1) {
                if (!empty($data['A'])) {
                    if ($user = user_load_by_mail($data['A'])) {
                      $user->set('field_first_name',$data['E']);
                        $user->set('field_last_name',$data['F']);
                        $user->set('field_contact_number',$data['G']);
                        $user->set('field_specialty',$data['H']);
                        $user->set('field_point',$data['D']);
                        $user->set('field_registration_no',$data['C']);
                        $user->set('field_mchk_license',$data['I']);
                        $user->set('field_membership_type',$data['J']);
                        $user->set('field_referee',$data['K']);
                        $user->set('field_specialist_fellow_number',$data['L']);
                        $userRolesArray = explode(',',$data['N']);//role id
                        $user->set('status',$data['M']);
                        if($data['M'] == 1){
                            $user->activate();
                        }
//                        $rolesobs = \Drupal::entityTypeManager()->getStorage('user_role')->loadMultiple();
//
//                        foreach($rolesobs as $r){
//                            $role = $r->toArray();
//                            $user->removeRole($role['rid']);
//                        }

                        foreach ($userRolesArray as $key => $role) {
                            $user->addRole($role);
                        }
                        try{
                            $user->save();

                            \Drupal::messenger()->addMessage('Update member '.$data['A'].' success.');
                        }catch (\Exception $e){
                            \Drupal::messenger()->addMessage('Update member '.$data['A'].' error '.$e->getMessage(),'error');
                        }

                    }else{
                        $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
                        $user = \Drupal\user\Entity\User::create();

                        //Mandatory settings
                        $user->setPassword(!empty($data['B'])? $data['B']:'hkdu123');
                        $user->enforceIsNew();
                        $user->setEmail($data['A']);
                        $user->setUsername($data['A']); //This username must be unique and accept only a-Z,0-9, - _ @ .

                        //Optional settings
                        $user->set("init", $data['A']);
                        $user->set("langcode", $language);
                        $user->set("preferred_langcode", $language);
                        $user->set("preferred_admin_langcode", $language);
                        $user->set('field_first_name',$data['E']);
                        $user->set('field_last_name',$data['F']);
                        $user->set('field_contact_number',$data['G']);
                        $user->set('field_specialty',$data['H']);
                        $user->set('field_point',$data['D']);
                        $user->set('field_registration_no',$data['C']);
                        $user->set('field_mchk_license',$data['I']);
                        $user->set('field_membership_type',$data['J']);
                        $user->set('field_referee',$data['K']);
                        $user->set('field_specialist_fellow_number',$data['L']);
                        $user->set('status',$data['M']);
                        $userRolesArray = explode(',',$data['N']);//role id
                       // $user->set('status',$data['M']);
                        foreach ($userRolesArray as $key => $role) {
                            $user->addRole($role);
                        }
                        //$user->set("setting_name", 'setting_value');
                        if($data['M'] == 1){
                            $user->activate();
                        }

                        try{
                            $user->save();
                            \Drupal::messenger()->addMessage('Create member '.$data['A'].' success.');
                        }catch (\Exception $e){
                            \Drupal::messenger()->addMessage('Create member '.$data['A'].' error '.$e->getMessage(),'error');
                        }
                    }
                }
            }

            $i++;
        }
    }

}
