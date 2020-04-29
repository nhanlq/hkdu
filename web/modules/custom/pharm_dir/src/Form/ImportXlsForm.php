<?php

namespace Drupal\pharm_dir\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ImportXlsForm.
 */
class ImportXlsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'import_xls_form';
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
            '#description' => $this->t('Allow Extension: xls, xlsx. Click <a target="_blank" href="/sites/default/files/import/pharm_dir_example.xlsx">here</a> to download example.')
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
//    foreach ($form_state->getValues() as $key => $value) {
//      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
//    }
        $user = \Drupal::currentUser();
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
                $image = file_get_contents($data['B']); // string
                $img_name = explode('/',$data['B']);
                end($img_name);         // move the internal pointer to the end of the array
                $key = key($img_name);  // fetches the key of the element pointed to by the internal pointer
                $file = file_save_data($image, 'public://'.$img_name[$key],FILE_EXISTS_REPLACE);
                $pharm_dir = \Drupal\pharm_dir\Entity\PharmDir::create([
                    'name' => $data['A'],
                    'uid' => $user->id(),
                    'field_image'=>$file->id(),
                    'field_description' => ['value' => $data['C'], 'format' => 'full_html'],
                    'field_address' => $data['D'],
                    'field_email' => $data['E'],
                    'field_fax' => $data['F'],
                    'field_phone' => $data['G'],
                    'field_website' => $data['H'],
                    'field_sales_visit' => $data['K'],
                    'status' => $data['L'],
                    'field_weight' => $data['M'],
                ]);
                $pharm_dir->set('field_order_form', [
                    'uri' => '<nolink>',
                    'title' => $data['I'],

                ]);
                $pharm_dir->set('field_price_list', [
                    'uri' => '<nolink>',
                    'title' => $data['J'],

                ]);
                try {
                    $pharm_dir->save();

                    \Drupal::messenger()->addMessage('Add '.$data['A'].' success.');
                }catch (\Exception $e){
                    \Drupal::messenger()->addMessage('Add '.$data['A'].' error: '.$e->getMessage(),'error');
                }
            }
            $i ++;
        }

    }

    public function getTagsTid($name)
    {
        $term_id = [];
        $termName = explode(',',$name);
        if(count($termName) > 1){
            foreach($termName as $n){
                $term = \Drupal::entityTypeManager()
                    ->getStorage('taxonomy_term')
                    ->loadByProperties(['name' => $n, 'vid' => 'epharm_tags']);
                if($term){
                    $term = reset($term);
                    $term_id[] = $term->id();
                }else{
                    $termNew = \Drupal\taxonomy\Entity\Term::create([
                        'name' => $n,
                        'vid' => 'epharm_tags'
                    ]);
                    $termNew->save();
                    $term_id[] = $termNew->id();
                }
            }
        }else{
            $term = \Drupal::entityTypeManager()
                ->getStorage('taxonomy_term')
                ->loadByProperties(['name' => $name, 'vid' => 'epharm_tags']);
            if($term){
                $term = reset($term);
                $term_id[] = $term->id();
            }else{
                $termNew = \Drupal\taxonomy\Entity\Term::create([
                    'name' => $name,
                    'vid' => 'epharm_tags'
                ]);
                $termNew->save();
                $term_id[] = $termNew->id();
            }
        }


        return $term_id;
    }

}
