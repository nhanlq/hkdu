<?php

namespace Drupal\cme_score\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ScoreCycleUser' block.
 *
 * @Block(
 *  id = "score_cycle_user",
 *  admin_label = @Translation("Score cycle user"),
 * )
 */
class ScoreCycleUser extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $current_path = \Drupal::service('path.current')->getPath();
        $path = explode('/', $current_path);
        $id = $path[2];
        if(!$id){
            $id = \Drupal::currentUser()->id();
        }
        $user = \Drupal\user\Entity\User::load($id);

        $build = [];
        $build['#theme'] = 'score_cycle_user';
        if($user->get('field_point')->value > 0){
            $content = '<div class="panel-heading"><div class="panel-title"></div></div><div class="Member-Cycle-Score"><p>Current Total Point: <strong>'.$user->get('field_point')->value .'</strong></p><p>'.$this->getScoreCurrentYear().'</p><p>'.$this->getScoreThreeYear().'</p></p></div>';

        }else{
            $content = '';
        }
        $build['score_cycle_user']['#markup'] = $content;

        return $build;
    }

    public function getScoreCurrentYear()
    {
      $user = \Drupal::currentUser();
      $year = strtotime(date('Y').'-01-01');
        $ids = \Drupal::entityQuery('score')
            ->condition('status', 1)
            ->condition('field_user', $user->id())
            ->condition('created', [$year, time()],'BETWEEN')
            ->execute();
        $results = \Drupal\cme_score\Entity\Score::loadMultiple($ids);
        $total = 0;
        if($results){
            foreach($results as $score){
                $total += $score->get('field_score')->value;
            }
        }

        return t('Total current year point (from '.date('Y',time()).'-01-01): ').'<strong> '.$total.'</strong>';
    }

    public function getScoreThreeYear()
    {

        $user = \Drupal::currentUser();
        $threeYear = intval(date('Y'))-3;
        $year = strtotime($threeYear.'-01-01');
        $ids = \Drupal::entityQuery('score')
            ->condition('status', 1)
            ->condition('field_user', $user->id())
            ->condition('created', [$year, time()],'BETWEEN')
            ->execute();
        $results = \Drupal\cme_score\Entity\Score::loadMultiple($ids);
        $total = 0;
        if($results){
            foreach($results as $score){
                $total += $score->get('field_score')->value;
            }
        }

        return t('Total 3 year point (from '.$threeYear.'-01-01): ').'<strong> '.$total.'</strong>';
    }


}
