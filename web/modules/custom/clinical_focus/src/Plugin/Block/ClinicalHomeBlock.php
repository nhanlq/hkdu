<?php

namespace Drupal\clinical_focus\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ClinicalHomeBlock' block.
 *
 * @Block(
 *  id = "clinical_home_block",
 *  admin_label = @Translation("Clinical home block"),
 * )
 */
class ClinicalHomeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
      return [
          '#theme' => ['clinical_focus_home'],
          '#clinical_focus' => $this->getClinical(),
          '#cache' => [
              'max-age' => 0,
          ],
      ];
  }


    public function getClinical()
    {
        $currentDate = time();
        $ids = \Drupal::entityQuery('clinical_focus')
            ->condition('status', 1)
            ->condition('field_expired',$currentDate,'>=')
            ->range(0,3)
            ->sort('field_weight','ASC')
            ->sort('field_publish_date','DESC')
            ->execute();
        $result = \Drupal\clinical_focus\Entity\ClinicalFocus::loadMultiple($ids);
        return $result;
    }

}
