<?php

namespace Drupal\event\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a 'EventHomeBlock' block.
 *
 * @Block(
 *  id = "event_home_block",
 *  admin_label = @Translation("Event home block"),
 * )
 */
class EventHomeBlock extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return [
            '#theme' => 'event_home',
            '#events' => $this->getEvent(),
            '#cache' => [
                'max-age' => 0,
            ],
        ];
    }

    public function getEvent()
    {
        $currentDate = time();
        $ids = \Drupal::entityQuery('event')
            ->condition('status', 1)
            ->condition('field_expired',$currentDate,'>=')
            ->range(0,3)
            ->sort('created','DESC')
            ->execute();

        $result = \Drupal\event\Entity\Event::loadMultiple($ids);
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
            ->condition('field_event', $event_id)
            ->condition('field_user', $uid)
            ->execute();
        $results = \Drupal\event_tracking\Entity\EventTracking::loadMultiple($ids);
        if($results){
            $return = true;
        }
        return $return;
    }


}
