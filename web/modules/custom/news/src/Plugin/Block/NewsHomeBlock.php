<?php

namespace Drupal\news\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'NewsHomeBlock' block.
 *
 * @Block(
 *  id = "news_home_block",
 *  admin_label = @Translation("News home block"),
 * )
 */
class NewsHomeBlock extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return [
            '#theme' => ['news_home'],
            '#news' => $this->getNews(),
            '#cache' => [
                'max-age' => 0,
            ],
        ];
    }

    public function getNews()
    {
//        $news = [];
//        $news[] = $this->getNewsByTid($this->getDrugNews('Drug News'));
//        $news[] = $this->getNewsByTid($this->getDrugNews('Clinical Focus'));
//        $news[] = $this->getNewsByTid($this->getDrugNews('Special Offers'));
        return $this->getNewsByTid();
    }

    public function getDrugNews($name)
    {
        $term = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties(['name' => $name, 'vid' => 'tags']);
        $term = reset($term);
        $term_id = $term->id();
        return $term_id;
    }

    public function getNewsByTid()
    {
        $new = false;
        $ids = \Drupal::entityQuery('news')
            ->condition('status', 1)
            ->sort('field_weight','ASC')
            ->sort('field_publish_date','DESC')
            ->range(0,3)
            ->execute();
        $result = \Drupal\news\Entity\News::loadMultiple($ids);
        return $result;

    }
}
