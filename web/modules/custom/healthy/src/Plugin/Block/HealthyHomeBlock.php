<?php

namespace Drupal\healthy\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\healthy\Entity\Healthy;

/**
 * Provides a 'HealthyHomeBlock' block.
 *
 * @Block(
 *  id = "healthy_home_block",
 *  admin_label = @Translation("Healthy home block"),
 * )
 */
class HealthyHomeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
      return [
              '#theme' => 'healthy_home',
              '#healthies' => $this->getAllHealthy(),
      ];
  }
    public function getAllHealthy()
    {
        $ids = \Drupal::entityQuery('healthy')
            ->condition('status', 1)
            ->range(0,5)
            ->sort('created','DESC')
            ->execute();
        $result = Healthy::loadMultiple($ids);
        return $result;
    }
}
