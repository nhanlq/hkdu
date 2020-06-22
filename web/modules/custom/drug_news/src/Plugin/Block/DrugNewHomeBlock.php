<?php

namespace Drupal\drug_news\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'DrugNewHomeBlock' block.
 *
 * @Block(
 *  id = "drug_new_home_block",
 *  admin_label = @Translation("Drug new home block"),
 * )
 */
class DrugNewHomeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
      return [
          '#theme' => ['drug_news_home'],
          '#drug_news' => $this->getDrug(),
          '#drug_search' => $this->getDrugSearch(),
          '#cache' => [
              'max-age' => 0,
          ],
      ];
  }

    public function getDrug()
    {
        $currentDate = time();
            $ids = \Drupal::entityQuery('drug_news')
                ->condition('status', 1)
                ->condition('field_is_home',1)
                ->condition('field_expired',$currentDate,'>=')
                ->range(0,5)
                ->sort('field_publish_date','DESC')
                ->execute();
        $result = \Drupal\drug_news\Entity\DrugNews::loadMultiple($ids);
        return $result;
    }

    public function getDrugSearch()
    {
        $currentDate = time();
        $ids = \Drupal::entityQuery('drug_search')
            ->condition('status', 1)
            ->condition('field_is_home',1)
            ->condition('field_expired',$currentDate,'>=')
            ->range(0,3)
            ->sort('field_publish_date','DESC')
            ->sort('name','ASC')
            ->execute();
        $result = \Drupal\drug_search\Entity\DrugSearch::loadMultiple($ids);
        return $result;
    }

}
