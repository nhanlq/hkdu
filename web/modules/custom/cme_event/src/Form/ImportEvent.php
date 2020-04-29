<?php

namespace Drupal\cme_event\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

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
      $form['audience'] = [
          '#type' => 'select',
          '#title' => 'Audience',
          '#options' => $this->getRoles(),
          '#multiple' => false,
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
          '#description' => $this->t('Allow Extension: xls, xlsx. Click <a target="_blank" href="/sites/default/files/import/cme_event.xlsx">here</a> to download example.')
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
      $user = \Drupal::currentUser();
      foreach ($sheetData as $data) {
          if($i != 1){
              //var_dump($data['M']);die;
              $member_price = new \Drupal\commerce_price\Price(number_format($data['M'],2), 'HKD');
              $price = new \Drupal\commerce_price\Price(number_format($data['L'],2), 'HKD');
              $date = date('Y-m-d', strtotime($data['D']));
              $publish_date = date('Y-m-d', strtotime($data['C']));
              $start_date = date('Y-m-d', strtotime($data['D']));
              $end_date = date('Y-m-d', strtotime($data['E']));
              $expired = date('Y-m-d', strtotime($data['F']));
              $event = \Drupal\cme_event\Entity\CmeEvent::create([
                  'name' => $data['A'],
                  'uid' => $user->id(),
                  'field_cme_point' => $data['B'],
                  'field_published_date' => $publish_date,
                  'field_start_date' => $start_date,
                  'field_date' =>$end_date,
                  'field_expired' =>$expired,
                  'field_start_time' => $data['G'],
                  'field_end_time' => $data['H'],
                  'field_speaker' => $data['I'],
                  'field_veune' => $data['J'],
                  'field_location' => $data['K'],
                  'field_price' => $price,
                  'field_member_price' => $member_price,
                  'field_organizer' => $this->getOrganizer($data['N'])? $this->getOrganizer($data['N']):null,
                  'field_free' => !empty($data['O']) ? $data['O'] : 0,
                  'field_description' => ['value' => $data['P'], 'format' => 'full_html'],
                  'status' => !empty($data['Q']) ? $data['Q'] : 0,
                  'field_tags' => !empty($data['R']) ? $this->getTagsTid($data['R']): null,
                  'field_audience' => $form_state->getValue('audience'),
                  'field_weight' => !empty($data['S']) ? $data['S'] : 0,
              ]);
              try {
                  $event->save();
                  \Drupal::messenger()->addMessage('Add '.$data['A'].' success.');
              }catch (\Exception $e){
                  \Drupal::messenger()->addMessage('Add '.$data['A'].' error: '.$e->getMessage(),'error');
              }
          }
          $i++;
      }
  }

  public function getRoles(){
      $roles = \Drupal::entityTypeManager()->getStorage('user_role')->loadMultiple();
      $r = [];
      foreach($roles as $role){
          $r[$role->id()] = $role->label();
      }
      return $r;
  }

    public function getTagsTid($name)
    {
        $term_id = [];
        $termName = explode(',',$name);
        if(count($termName) > 1){
            foreach($termName as $n){
                $term = \Drupal::entityTypeManager()
                    ->getStorage('taxonomy_term')
                    ->loadByProperties(['name' => $n, 'vid' => 'event']);
                if($term){
                    $term = reset($term);
                    $term_id[] = $term->id();
                }else{
                    $termNew = \Drupal\taxonomy\Entity\Term::create([
                        'name' => $n,
                        'vid' => 'event'
                    ]);
                    $termNew->save();
                    $term_id[] = $termNew->id();
                }
            }
        }else{
            $term = \Drupal::entityTypeManager()
                ->getStorage('taxonomy_term')
                ->loadByProperties(['name' => $name, 'vid' => 'event']);
            if($term){
                $term = reset($term);
                $term_id[] = $term->id();
            }else{
                $termNew = \Drupal\taxonomy\Entity\Term::create([
                    'name' => $name,
                    'vid' => 'event'
                ]);
                $termNew->save();
                $term_id[] = $termNew->id();
            }
        }


        return $term_id;
    }
    public function getOrganizer($name){
        $ids = \Drupal::entityQuery('cme_event')
            ->condition('status', 1)
            ->condition('name',$name)
            ->execute();
        $result = \Drupal\cme_event\Entity\CmeEvent::loadMultiple($ids);
        if($result){
            $r = reset($result);
            return $r->id();
        }else{
            return false;
        }
    }
}
