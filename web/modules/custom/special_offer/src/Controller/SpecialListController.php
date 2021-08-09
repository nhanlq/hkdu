<?php

namespace Drupal\special_offer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\special_offer\Entity\SpecialOffer;
use Drupal\taxonomy\Entity\Term;

/**
 * Class SpecialListController.
 */
class SpecialListController extends ControllerBase {

  /**
   * List.
   *
   * @return string
   *   Return Hello string.
   */
  public function list() {
    $tags = NULL;
    if (isset($_GET['tags'])) {
      $tags = $_GET['tags'];
    }
    $search = '';
    if (isset($_GET['keys'])) {
      $search = $_GET['keys'];
    }
    return [
      'results' => [
        '#theme' => 'special_offer_list',
        '#special_offer' => $this->getDrug(),
        '#tags' => $this->getTags(),
        '#get' => $tags,
        '#search' => $search,
      ],
      'pager' => [
        '#type' => 'pager',
      ],
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  /**
   * @return \Drupal\Core\Entity\EntityBase[]|\Drupal\Core\Entity\EntityInterface[]|\Drupal\special_offer\Entity\SpecialOffer[]\
   */
  public function getDrug() {
    $tid = FALSE;
    if (isset($_GET['tags'])) {
      $tags = $_GET['tags'];
      $tid = $this->getTagsTid($tags);
    }
    if ($tid) {
      $ids = \Drupal::entityQuery('special_offer')
        ->condition('status', 1)
        ->condition('id',$this->getDrugUsers(),'NOT IN')
        ->condition('field_tags', $tid)
        ->sort('field_weight', 'ASC')
        ->sort('field_publish_date', 'DESC')
        ->pager(10)
        ->execute();
    }
    elseif (isset($_GET['keys'])) {
      $ids1 = \Drupal::entityQuery('special_offer')
        ->condition('status', 1)
        ->condition('name', $_GET['keys'], 'CONTAINS')
        ->condition('id',$this->getDrugUsers(),'NOT IN')
        ->sort('field_weight', 'ASC')
        ->sort('field_publish_date', 'DESC')
        ->pager(10)
        ->execute();
      $ids2 = \Drupal::entityQuery('special_offer')
        ->condition('status', 1)
        ->condition('field_description', $_GET['keys'], 'CONTAINS')
        ->condition('id',$this->getDrugUsers(),'NOT IN')
        ->sort('field_weight', 'ASC')
        ->sort('field_publish_date', 'DESC')
        ->pager(10)
        ->execute();
      $ids = array_merge($ids1, $ids2);
    }
    else {

      $ids = \Drupal::entityQuery('special_offer')
        ->condition('status', 1)
        ->condition('id',$this->getDrugUsers(),'NOT IN')
        ->sort('field_weight', 'ASC')
        ->sort('field_publish_date', 'DESC')
        ->pager(10)
        ->execute();
    }
    $result = SpecialOffer::loadMultiple($ids);
    return $result;
  }

  /**
   * @return array
   */
  public function getTags() {
    $tags = [];
    $ids = \Drupal::entityQuery('special_offer')->condition('status', 1)->execute();
    $result = SpecialOffer::loadMultiple($ids);
    foreach ($result as $drug) {
      foreach ($drug->get('field_tags')->getValue() as $tag) {
        $term = Term::load($tag['target_id']);
        $tags[$tag['target_id']] = $term->getName();
      }
    }
    return $tags;
  }

  /**
   * @param $name
   *
   * @return int|string|null
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getTagsTid($name) {
    $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties([
        'name' => $name,
        'vid' => 'epharm_tags',
      ]);
    $term = reset($term);
    $term_id = $term->id();
    return $term_id;
  }

  /**
   * @return array
   */
  public function title() {
    return [
      '#markup' => \Drupal::state()->get('/e-pharm/special-offer', 'Special Offers'),
      '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList(),
    ];
  }
  /**
   * get all drug users
   */
  public function getDrugUsers(){
    $user = \Drupal::currentUser();
    $uids = [];
    $id = [];

    if(in_array('drug_suppliers',$user->getRoles())){
      $uid = $user->id();
    }else{
      $uid = false;
    }
    if($uid){
      $ids = \Drupal::entityQuery('user')
        ->condition('status', 1)
        ->condition('uid', $uid,'<>')
        ->condition('roles', 'drug_suppliers')
        ->execute();
      $users = \Drupal\user\Entity\User::loadMultiple($ids);
      foreach($users as $user){
        $uids[] = $user->id();
      }
      $spd = \Drupal::entityQuery('special_offer')
        ->condition('status', 1)
        ->condition('user_id', $uids,'IN')
        ->execute();
      $result = \Drupal\special_offer\Entity\SpecialOffer::loadMultiple($spd);
      if($result){
        foreach($result as $sp){
          $id[] = $sp->id();
        }
      }else{
        $id[] = 0;
      }

    }else{
      $id[] = 0;
    }
    return $id;
  }

}
