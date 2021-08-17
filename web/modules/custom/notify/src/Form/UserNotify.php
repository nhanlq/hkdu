<?php

namespace Drupal\notify\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class UserNotify.
 */
class UserNotify extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user_notify';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['uid'] = [
      '#type' => 'select',
      '#title' => t('Assignee'),
      '#options' => $this->getContentReviewers(),
      '#description' => t('Please choose Content Reviewer'),
    ];
    $form['type'] = [
      '#type' => 'checkboxes',
      '#title' => 'Article Type',
      '#options' => $this->getType(),
      '#required' => true,
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
    $type = [];
    foreach($form_state->getValue('type') as $val){
      if($val !== 0){
        $type[] = $val;
      }
    }
    $user = \Drupal\user\Entity\User::load($form_state->getValue('uid'));
    $notify = \Drupal\notify\Entity\UserNotify::create([
      'name' => $user->get('field_first_name')->value,
      'field_user' => $user->id(),
      'field_type' => $type,
    ]);
    $notify->save();
    \Drupal::messenger()->addMessage('Reviewer '.$user->get('field_first_name')->value.' has added to '.implode(', ',$type));
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
   * @return string[]
   */
  public function getType() {
    return [
      'clinical_focus' => 'Clinical Focus',
      'drug_news' => 'Drug News',
      'special_offer' => 'Special Offer',
      'pharm_dir' => 'Pharm Dir',
      'forum' => 'Forum',
      'ads' => 'Classified Ads',
      'know' => 'Dug Databases',
      'bulletin' => 'Bulletin',
      'committee_news' => 'Committee News',
      'member_article' => 'Council News',
      'event_calendar' => 'Event Calendar',
      'faq' => 'FAQ',
      'image_gallery' => 'Image Gallery',
    ];
  }

//  /**
//   * @return \Drupal\Core\Entity\EntityBase[]|\Drupal\Core\Entity\EntityInterface[]|\Drupal\notify\Entity\UserNotify[]
//   */
//  public function getUsers() {
//    $ids = \Drupal::entityQuery('user_notify')->execute();
//    $result = \Drupal\notify\Entity\UserNotify::loadMultiple($ids);
//    foreach ($result as $notify) {
//      $data = [];
//      foreach ($notify->get('field_type')->getValue() as $type) {
//        $data[] = $type['value'];
//      }
//      $result->typeData = $data;
//    }
//    return [
//      '#theme' => ['notify_user_list'],
//      '#users' => $result,
//      '#cache' => [
//        'max-age' => 0,
//      ],
//    ];
//  }

}
