<?php

namespace Drupal\cme_quiz\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class QuizListController.
 */
class QuizListController extends ControllerBase {

  /**
   * Quizlist.
   *
   * @return string
   *   Return Hello string.
   */
  public function quizlist() {
      $tags = null;
      if (isset($_GET['tags'])) {
          $tags = $_GET['tags'];
      }
      $search = '';
      if (isset($_GET['keys'])) {
          $search = $_GET['keys'];
      }
      return [
          'results' => [
              '#theme' => 'quiz_list',
              '#quizs' => $this->getQuiz(),
              '#tags' => $this->getTags(),
              '#get' => $tags,
              '#search' => $search,
          ],
          'pager' => [
              '#type' => 'pager',
          ],
      ];
  }
    public function getQuiz()
    {
        $tid = False;
        if (isset($_GET['tags'])) {
            $tags = $_GET['tags'];
            $tid = $this->getTagsTid($tags);
        }
        $currentDate = time();

        if ($tid) {
            $ids = \Drupal::entityQuery('quiz')
                ->condition('status', 1)
                ->condition('field_category', $tid)
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->sort('created','DESC')
                ->pager(15)
                ->execute();
        } elseif (isset($_GET['keys'])) {
            $ids = \Drupal::entityQuery('quiz')
                ->condition('status', 1)
                ->condition('name', $_GET['keys'], 'CONTAINS')
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->sort('created','DESC')
                ->pager(15)
                ->execute();
        } else {
            $ids = \Drupal::entityQuery('quiz')
                ->condition('status', 1)
                ->condition('field_expired',$currentDate,'>=')
                ->sort('field_weight','ASC')
                ->sort('created','DESC')
                ->pager(15)
                ->execute();
        }
        $result = \Drupal\cme_quiz\Entity\Quiz::loadMultiple($ids);
        foreach($result as $r){

            $r->author = $this->getAuthor($r->getOwnerId());
            if($this->checkExpiredQuiz($r)){
                $r->expired = true;
            }else{
                $r->expired = false;
            }
            if($this->checkQuizStatusUser($r->id())){
                $r->check_event = true;
                if($this->checkUserAttempStatus($r)){
                    $r->attemp = 'Passed';
                }else{
                    $r->attemp = 'Attempted';
                }
            }else{
                $r->check_event = false;
            }
        }
        return $result;
    }

    public function getTags()
    {
        $tags = [];
        $ids = \Drupal::entityQuery('quiz')
            ->condition('status', 1)
            ->execute();
        $result = \Drupal\cme_quiz\Entity\Quiz::loadMultiple($ids);
        foreach ($result as $drug) {
            foreach ($drug->get('field_category')->getValue() as $tag) {
                $term = \Drupal\taxonomy\Entity\Term::load($tag['target_id']);
                $tags[$tag['target_id']] = $term->getName();
            }
        }
        return $tags;
    }

    public function getTagsTid($name)
    {
        $term = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties(['name' => $name, 'vid' => 'quiz']);
        $term = reset($term);
        $term_id = $term->id();
        return $term_id;
    }


    /**
     * get product by Event
     */
    public function getQuestions($quiz_id){
        $ids = \Drupal::entityQuery('question')
            ->condition('type', 'default')
            ->condition('status', 1)
            ->condition('field_quiz', $quiz_id)
            ->execute();
        $results = \Drupal\cme_question\Entity\Question::loadMultiple($ids);
        $result = reset($results);
        return $result;
    }

    public function checkQuizStatusUser($quiz_id){
        $return = null;
        $user = \Drupal::currentUser();
        $uid = $user->id();
        $ids = \Drupal::entityQuery('result')
            ->condition('status', 1)
            ->condition('field_quiz', $quiz_id)
            ->condition('field_user', $uid)
            ->execute();
        $results = \Drupal\cme_result\Entity\Result ::loadMultiple($ids);
        if($results){
            $return = true;
        }
        return $return;
    }

    public function checkExpiredQuiz($quiz){
        $current = time();
        $date = strtotime($quiz->get('field_expired')->value.' 00:00:00');
        if($current > $date){
            return true;
        }else{
            return false;
        }
    }
    public function checkUserAttempStatus($quiz){
        $return = null;
        $user = \Drupal::currentUser();
        $uid = $user->id();
        $ids = \Drupal::entityQuery('result')
            ->condition('status', 1)
            ->condition('field_quiz', $quiz->id())
            ->condition('field_user', $uid)
            ->execute();
        $results = \Drupal\cme_result\Entity\Result ::loadMultiple($ids);
        if($results){
           foreach($results as $result){
               if($result->get('field_passed')->value == 1){
                   $return = true;
                   break;
               }
           }
        }
        return $return;
    }
    public function getAuthor($uid){

        $user = \Drupal\user\Entity\User::load($uid);
        $account = $user->toArray();
        $name = '';
        if($account['field_first_name']){
            $name .= $account['field_first_name'][0]['value'];
        }
        if($account['field_last_name']){
            $name .= ' '.$account['field_last_name'][0]['value'];
        }
        if($name == ''){
            $name .= $account['name'][0]['value'];
        }
        return $name;
    }
}
