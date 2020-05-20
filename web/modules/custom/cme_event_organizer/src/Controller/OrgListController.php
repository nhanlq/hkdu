<?php

namespace Drupal\cme_event_organizer\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class OrgListController.
 */
class OrgListController extends ControllerBase {

  /**
   * List.
   *
   * @return string
   *   Return Hello string.
   */
    public function list()
    {

        return [
            'results' => [
                '#theme' => 'cme_event_organizer_list',
                '#orgs' => $this->getHospitals(),
            ],
            'pager' => [
                '#type' => 'pager',
            ],
        ];
    }

    public function getHospitals()
    {
        $ids = \Drupal::entityQuery('event_org')
            ->condition('status', 1)
            ->sort('field_weight','ASC')
            ->sort('created','DESC')
            ->pager(15)
            ->execute();
        $result = \Drupal\cme_event_organizer\Entity\EventOrg::loadMultiple($ids);
        return $result;
    }
    public function title(){
        return ['#markup' => \Drupal::state()->get('/cme/event-org','Event Organizers'), '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList()];
    }

}
