<?php

namespace Drupal\epharm\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class CloneEntityController.
 */
class CloneEntityController extends ControllerBase {

  /**
   * Clone.
   *
   * @return string
   *   Return Hello string.
   */
  public function clone($type, $id) {

      $replicator = \Drupal::service('replicate.replicator');

    switch ($type){
        case 'event':
            $entity = \Drupal\event\Entity\Event::load($id);
            $duplicate_entity = $replicator->replicateByEntityId($entity->getEntityTypeId(), $entity->id());
            $title = $duplicate_entity->getName();
            $duplicate_entity->setName($title . ' - Cloned');
            $request_time = \Drupal::time()->getRequestTime();
            $duplicate_entity->setCreatedTime($request_time);
            $duplicate_entity->save();
            $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/admin/hkdu/event/'.$duplicate_entity->id().'/edit?destination=/admin/hkdu/manage-events')->toString());
            $redirect->send();
            break;
        case 'clinical_focus':
            $entity = \Drupal\clinical_focus\Entity\ClinicalFocus::load($id);
            $duplicate_entity = $replicator->replicateByEntityId($entity->getEntityTypeId(), $entity->id());
            $title = $duplicate_entity->getName();
            $duplicate_entity->setName($title . ' - Cloned');
            $request_time = \Drupal::time()->getRequestTime();
            $duplicate_entity->setCreatedTime($request_time);
            $duplicate_entity->save();
            $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/admin/hkdu/clinical-focus/'.$duplicate_entity->id().'/edit?destination=/admin/e-pharm/manage-clinical-focus')->toString());
            $redirect->send();
            break;
        case 'drug_news':
            $entity = \Drupal\drug_news\Entity\DrugNews::load($id);
            $duplicate_entity = $replicator->replicateByEntityId($entity->getEntityTypeId(), $entity->id());
            $title = $duplicate_entity->getName();
            $duplicate_entity->setName($title . ' - Cloned');
            $request_time = \Drupal::time()->getRequestTime();
            $duplicate_entity->setCreatedTime($request_time);
            $duplicate_entity->save();
            $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/admin/hkdu/drug-news/'.$duplicate_entity->id().'/edit?destination=/admin/hkdu/manage-drug-news')->toString());
            $redirect->send();
            break;
        case 'drug_search':
            $entity = \Drupal\drug_search\Entity\DrugSearch::load($id);
            $duplicate_entity = $replicator->replicateByEntityId($entity->getEntityTypeId(), $entity->id());
            $title = $duplicate_entity->getName();
            $duplicate_entity->setName($title . ' - Cloned');
            $request_time = \Drupal::time()->getRequestTime();
            $duplicate_entity->setCreatedTime($request_time);
            $duplicate_entity->save();
            $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/admin/hkdu/drug-search/'.$duplicate_entity->id().'/edit?destination=/admin/hkdu/manage-drug-search')->toString());
            $redirect->send();
            break;
        case 'pharm_dir':
            $entity = \Drupal\pharm_dir\Entity\PharmDir::load($id);
            $duplicate_entity = $replicator->replicateByEntityId($entity->getEntityTypeId(), $entity->id());
            $title = $duplicate_entity->getName();
            $duplicate_entity->setName($title . ' - Cloned');
            $request_time = \Drupal::time()->getRequestTime();
            $duplicate_entity->setCreatedTime($request_time);
            $duplicate_entity->save();
            $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/admin/hkdu/pharm-dir/'.$duplicate_entity->id().'/edit?destination=/admin/hkdu/e-pharm/pham-dir/manage')->toString());
            $redirect->send();
            break;
        case 'special_offer':
            $entity = \Drupal\special_offer\Entity\SpecialOffer::load($id);
            $duplicate_entity = $replicator->replicateByEntityId($entity->getEntityTypeId(), $entity->id());
            $title = $duplicate_entity->getName();
            $duplicate_entity->setName($title . ' - Cloned');
            $request_time = \Drupal::time()->getRequestTime();
            $duplicate_entity->setCreatedTime($request_time);
            $duplicate_entity->save();
            $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/admin/hkdu/special-offer/'.$duplicate_entity->id().'/edit?destination=/admin/hkdu/manage-special-offers')->toString());
            $redirect->send();
            break;
        case 'cme_event':
            $entity = \Drupal\cme_event\Entity\CmeEvent::load($id);
            $duplicate_entity = $replicator->replicateByEntityId($entity->getEntityTypeId(), $entity->id());
            $title = $duplicate_entity->getName();
            $duplicate_entity->setName($title . ' - Cloned');
            $request_time = \Drupal::time()->getRequestTime();
            $duplicate_entity->setCreatedTime($request_time);
            $duplicate_entity->save();
            $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/admin/cme/cme-event/'.$duplicate_entity->id().'/edit?destination=/admin/cme/manage-events')->toString());
            $redirect->send();
            break;
        case 'quiz':
            $entity = \Drupal\cme_quiz\Entity\Quiz::load($id);
            $duplicate_entity = $replicator->replicateByEntityId($entity->getEntityTypeId(), $entity->id());
            $title = $duplicate_entity->getName();
            $duplicate_entity->setName($title . ' - Cloned');
            $request_time = \Drupal::time()->getRequestTime();
            $duplicate_entity->setCreatedTime($request_time);
            $duplicate_entity->save();
            $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/admin/cme/quiz/'.$duplicate_entity->id().'/edit?destination=/admin/cme/manage-quizzes')->toString());
            $redirect->send();
            break;
    }

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Clone success')
    ];
  }

}
