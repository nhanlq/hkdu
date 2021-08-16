<?php

namespace Drupal\notify\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\notify\Entity\Notify;

/**
 * Class Assign.
 */
class Assign extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'assign';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL, $type = NULL) {

    $form['uid'] = [
      '#type' => 'select',
      '#title' => t('Assignee'),
      '#options' => $this->getContentReviewers(),
      '#description' => t('Please choose Content Reviewer'),
    ];
    $form['type'] = [
      '#type' => 'hidden',
      '#value' => $type,
    ];
    $form['id'] = [
      '#type' => 'hidden',
      '#value' => $id,
    ];
    $form['call_back'] = [
      '#type' => 'hidden',
      '#value' => $_GET['destination'],
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Assign'),
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
    $id = $form_state->getValue('id');
    $type = $form_state->getValue('type');
    $user = \Drupal\user\Entity\User::load($form_state->getValue('uid'));
    $entity = $this->getObject($id, $type);
    if($type == 'node'){
      $entity_type = $entity->bundle();
    }else{
      $entity_type = $type;
    }
    $notify = Notify::create([
      'name' => $this->mappTypeName($id, $type) . ' from ' . $user->get('field_first_name')->value . ' approval',
      'field_link' => $this->getLink($id, $type),
      'field_user' => $form_state->getValue('uid'),
      'field_type' => $entity_type,
      'field_id' => $id,
      'user_id' => $form_state->getValue('uid'),
      'created' => time(),
      'status' => 1,
    ]);
    $notify->save();
    $users = $this->getadmins();
    foreach($users as $admin){
      $notify_admin = Notify::create([
        'name' => $this->mappTypeName($id, $type) . ' from ' . $user->get('field_first_name')->value . ' approval',
        'field_link' => $this->getLink($id, $type),
        'field_user' => $admin->id(),
        'field_type' => $entity_type,
        'field_id' => $id,
        'user_id' => $admin->id(),
        'created' => time(),
        'status' => 1,
      ]);
      $notify_admin->save();
    }
    \Drupal::messenger()->addMessage('Assign '.$this->mappTypeName($id, $type).' "'.$this->getName($id, $type).'" to '.$user->get('field_first_name')->value.' success');
    $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput($form_state->getValue('call_back'))->toString());;
    $redirect->send();
  }

  /**
   * @param $id
   * @param $type
   *
   * @return \Drupal\clinical_focus\Entity\ClinicalFocus|\Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface|\Drupal\drug_news\Entity\DrugNews|\Drupal\node\Entity\Node|\Drupal\pharm_dir\Entity\PharmDir|\Drupal\special_offer\Entity\SpecialOffer|null
   */
  public function getObject($id, $type) {
    if ($type == 'node') {
      $entity = \Drupal\node\Entity\Node::load($id);
    }
    else {
      if ($type == 'clinical_focus') {
        $entity = \Drupal\clinical_focus\Entity\ClinicalFocus::load($id);
      }
      if ($type == 'drug_news') {
        $entity = \Drupal\drug_news\Entity\DrugNews::load($id);
      }
      if ($type == 'special_offer') {
        $entity = \Drupal\special_offer\Entity\SpecialOffer::load($id);
      }
      if ($type == 'pharm_dir') {
        $entity = \Drupal\pharm_dir\Entity\PharmDir::load($id);
      }
    }
    return $entity;
  }

  /**
   * @return array
   */
  public function getContentReviewers() {
    $ids = \Drupal::entityQuery('user')->condition('status', 1)->condition('roles', 'content_reviewer')->execute();
    $users = \Drupal\user\Entity\User::loadMultiple($ids);
    $data = [];
    foreach ($users as $user) {
      $data[$user->id()] = $user->get('field_first_name')->value . '(' . $user->getEmail() . ')';
    }
    return $data;
  }

  /**
   * @return array
   */
  public function getadmins() {
    $ids = \Drupal::entityQuery('user')->condition('status', 1)->condition('roles', ['admins','administrator'],'IN')->execute();
    $users = \Drupal\user\Entity\User::loadMultiple($ids);
    $data = [];
    foreach ($users as $user) {
      $data[$user->id()] = $user;
    }
    return $data;
  }

  /**
   * @param $id
   * @param $type
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|mixed|string
   */
  public function mappTypeName($id, $type) {
    $object = $this->getObject($id, $type);
    $name = '';
    if ($type == 'node') {
      $bundle_info = \Drupal::service("entity_type.bundle.info")->getAllBundleInfo();
      $name = $bundle_info[$object->getEntityTypeId()][$object->bundle()]['label'];
    }
    else {
      $name = $object->getEntityType()->getLabel();
    }
    return $name;
  }

  /**
   * @param $id
   * @param $type
   *
   * @return string
   */
  public function getLink($id, $type) {
    $link = '';
    if ($type == 'node') {
      $link = '/node/' . $id . '/edit';
    }
    else {
      if ($type == 'clinical_focus') {
        $link = '/admin/hkdu/clinical-focus/' . $id . '/edit';
      }
      if ($type == 'drug_news') {
        $link = '/admin/hkdu/drug-news/' . $id . '/edit';
      }
      if ($type == 'special_offer') {
        $link = '/admin/hkdu/special-offer/' . $id . '/edit';
      }
      if ($type == 'pharm_dir') {
        $link = '/admin/hkdu/pharm-dir/'.$id.'/edit';
      }
    }
    return $link;
  }

  public function getName($id, $type){
    $object = $this->getObject($id, $type);
    if ($type == 'node') {
      $name = $object->getTitle();
    }
    else {
      $name = $object->getName();
    }
    return $name;
  }
}
