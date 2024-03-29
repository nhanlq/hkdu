<?php

namespace Drupal\epharm\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'MenuEpharmBlock' block.
 *
 * @Block(
 *  id = "menu_epharm_block",
 *  admin_label = @Translation("Menu epharm block"),
 * )
 */
class MenuEpharmBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $epharm = FALSE;
    $member = FALSE;
    $cme = FALSE;
    $current_path = \Drupal::service('path.current')->getPath();
    $ep = explode('/', $current_path);
    $au = 0;
    $role_cme = 0;
    $role_member = 0;
    $role_doctor = 0;
    $current_user = \Drupal::currentUser();
    $current_roles = $current_user->getRoles();
    if (in_array('administrator', $current_roles) || in_array('admins', $current_roles)) {
      $au = 1;
    }

    if (in_array('cme_member', $current_roles)) {
      $role_cme = 1;
      $role_doctor = 1;
    }
    if (in_array('hkdu_members', $current_roles) || in_array('council_members', $current_roles) || in_array('tester', $current_roles)) {
      $role_member = 1;
      $role_doctor = 1;
      $role_cme = 1;
    }
    if (in_array('drug_suppliers', $current_roles)) {
      $role_doctor = 1;
      $role_member = 1;
    }
    if (in_array('doctor', $current_roles)) {
      $role_doctor = 1;
    }
    $id = $ep[1];
    if ($id == 'e-pharm') {
      $epharm = $id;
    }
    if ($id == 'node' && is_numeric($ep[2])) {
      $node = \Drupal\node\Entity\Node::load($ep[2]);
      if (in_array($node->bundle(),$this->listType())) {
        $member = $id;
      }
    }

    if ($id == 'member-area') {
      $member = $id;
    }
    if ($id == 'cme') {
      $cme = $id;
    }
    return [
      '#theme' => ['epharm_menu_block'],
      '#epharm' => $epharm,
      '#admin' => $au,
      '#cme' => $cme,
      '#role_doctor' =>$role_doctor,
      '#role_cme' =>$role_cme,
      '#role_member' =>$role_member,
      '#member' => $member,

      '#attached' => [
        'library' => [
          'epharm/epharm_menu',
        ],
      ],
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  public function listType() {
    return [
      'member_article',
      'forum',
      'bulletin',
      'image_gallery',
      'faq',
      'sharing',
      'know',
      'committee_news',
      'ads',
      'event_calendar',
    ];
  }

}
