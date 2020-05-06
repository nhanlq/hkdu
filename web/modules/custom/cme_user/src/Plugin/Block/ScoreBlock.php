<?php

namespace Drupal\cme_user\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ScoreBlock' block.
 *
 * @Block(
 *  id = "score_block",
 *  admin_label = @Translation("Score block"),
 * )
 */
class ScoreBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'score_block';
     $build['score_block']['#markup'] = 'Implement ScoreBlock.';

    return $build;
  }

}
