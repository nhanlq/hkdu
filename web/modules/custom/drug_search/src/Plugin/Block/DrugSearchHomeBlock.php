<?php

namespace Drupal\drug_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'DrugSearchHomeBlock' block.
 *
 * @Block(
 *  id = "drug_search_home_block",
 *  admin_label = @Translation("Drug search home block"),
 * )
 */
class DrugSearchHomeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'drug_search_home_block';
     $build['drug_search_home_block']['#markup'] = 'Implement DrugSearchHomeBlock.';

    return $build;
  }

}
