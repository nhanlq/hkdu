<?php

namespace Drupal\cme_event\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'HomeBlock' block.
 *
 * @Block(
 *  id = "home_block",
 *  admin_label = @Translation("CME Event Home Block"),
 * )
 */
class HomeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
    public function build()
    {
        return [
            '#theme' => 'cme_event_home',
            '#events' => $this->getEvent(),
            '#cache' => [
                'max-age' => 0,
            ],
        ];
    }

    public function getEvent()
    {
        $currentDate = time();
        $ids = \Drupal::entityQuery('cme_event')
            ->condition('status', 1)
            ->condition('field_is_home',1)
            ->condition('field_expired',$currentDate,'>=')
            ->range(0,3)
            ->sort('field_weight','ASC')
            ->sort('created','DESC')
            ->execute();

        $result = \Drupal\cme_event\Entity\CmeEvent::loadMultiple($ids);
        foreach($result as $r){
            if($this->checkEventStatusUser($r->id())){
                $r->check_event = true;
            }else{
                $r->check_event = false;
            }
        }
        return $result;
    }

    public function checkEventStatusUser($event_id){
        $return = null;
        $user = \Drupal::currentUser();
        $uid = $user->id();
        $ids = \Drupal::entityQuery('event_tracking')
            ->condition('status', 1)
            ->condition('field_cme_event', $event_id)
            ->condition('field_user', $uid)
            ->execute();
        $results = \Drupal\event_tracking\Entity\EventTracking::loadMultiple($ids);
        if($results){
            $return = true;
        }
        return $return;
    }

}
