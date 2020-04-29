<?php

namespace Drupal\tools\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class ToolListController.
 */
class ToolListController extends ControllerBase {

  /**
   * List.
   *
   * @return string
   *   Return Hello string.
   */
  public function list() {
      return [
          'results' => [
              '#theme' => 'tools_list',
              '#tools' => $this->getTools(),
          ],
          'pager' => [
              '#type' => 'pager',
          ],
      ];
  }

    public function getTools()
    {

            $ids = \Drupal::entityQuery('tools')
                ->condition('status', 1)
                ->sort('field_weight','ASC')
                ->pager(10)
                ->execute();
        $result = \Drupal\tools\Entity\Tools::loadMultiple($ids);
        return $result;
    }

}
