<?php

namespace Drupal\cme\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class ImportController.
 */
class ImportController extends ControllerBase {

  /**
   * Import.
   *
   * @return string
   *   Return Hello string.
   */
  public function import() {
      $this->importUser();
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Import success.')
    ];
  }

  public function importUser(){
      $query = \Drupal::database()->select('savsoft_users', 'su');
      $query->fields('su');
      $result = $query->execute()->fetchAll();
      foreach($result as $r){
          if(!user_load_by_mail($r->email)){
              $user = \Drupal\user\Entity\User::create();
              $user->setPassword($r->password);
              $user->enforceIsNew();
              $user->setEmail($r->email);
              $user->setUsername($r->email);
              $user->set('field_first_name', $r->first_name);
              $user->set('field_last_name', $r->last_name);
              $user->set('field_contact_number', $r->contact_no);
              $user->set('field_registration_no', !empty($r->hkdu) ? $r->hkdu : $r->uid);
              $user->activate();
              //Adding default user roles
              $user->addRole('cme_member'); //E.g: authenticated
              $user->save();
          }

      }
      return true;
  }

}
