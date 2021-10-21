<?php

namespace Drupal\cme_quiz\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
    $tags = NULL;
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
        '#cache' => [
          'max-age' => 0,
        ],
      ],
      'pager' => [
        '#type' => 'pager',
      ],
    ];
  }

  /**
   * Quizlist.
   *
   * @return string
   *   Return Hello string.
   */
  public function quizlistarchived() {
    $tags = NULL;
    if (isset($_GET['tags'])) {
      $tags = $_GET['tags'];
    }
    $search = '';
    if (isset($_GET['keys'])) {
      $search = $_GET['keys'];
    }
    return [
      'results' => [
        '#theme' => 'quiz_list_archived',
        '#quizs' => $this->getQuizArchived(),
        '#tags' => $this->getTags(),
        '#get' => $tags,
        '#search' => $search,
        '#cache' => [
          'max-age' => 0,
        ],
      ],
      'pager' => [
        '#type' => 'pager',
      ],
    ];
  }

  /**
   * @return \Drupal\cme_quiz\Entity\Quiz[]|\Drupal\Core\Entity\EntityBase[]|\Drupal\Core\Entity\EntityInterface[]
   */
  public function getQuiz() {
    $tid = FALSE;
    if (isset($_GET['tags'])) {
      $tags = $_GET['tags'];
      $tid = $this->getTagsTid($tags);
    }
    $now = new DrupalDateTime('now');
    if ($tid) {
      $ids = \Drupal::entityQuery('quiz')
        ->condition('status', 1)
        ->condition('field_category', $tid)
        ->condition('field_expired', $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '>=')
        ->sort('field_weight', 'ASC')
        ->sort('created', 'DESC')
        ->pager(15)
        ->execute();
    }
    elseif (isset($_GET['keys'])) {
      $ids = \Drupal::entityQuery('quiz')
        ->condition('status', 1)
        ->condition('name', $_GET['keys'], 'CONTAINS')
        ->condition('field_expired', $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '>=')
        ->sort('field_weight', 'ASC')
        ->sort('created', 'DESC')
        ->pager(15)
        ->execute();
    }
    else {
      $ids = \Drupal::entityQuery('quiz')
        ->condition('status', 1)
        ->condition('field_expired', $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '>=')
        ->sort('field_weight', 'ASC')
        ->sort('created', 'DESC')
        ->pager(15)
        ->execute();
    }
    $result = \Drupal\cme_quiz\Entity\Quiz::loadMultiple($ids);
    foreach ($result as $r) {
      $pse = [];
      foreach($r->get('field_specialty')->getValue() as $sp){
        $term = \Drupal\taxonomy\Entity\Term::load($sp['target_id']);
        $pse[] = $term->getName();
      }
      $r->specialist = $pse;
      $r->author = $this->getAuthor($r->getOwnerId());
      if ($this->checkExpiredQuiz($r)) {
        $r->expired = TRUE;
      }
      else {
        $r->expired = FALSE;
      }
      if ($this->checkQuizStatusUser($r->id())) {
        $r->check_event = TRUE;
        if ($this->checkUserAttempStatus($r)) {
          $r->attemp = 'Passed';
        }
        else {
          $r->attemp = 'Attempted';
        }
      }
      else {
        $r->check_event = FALSE;
      }
    }
    return $result;
  }

  /**
   * @return \Drupal\cme_quiz\Entity\Quiz[]|\Drupal\Core\Entity\EntityBase[]|\Drupal\Core\Entity\EntityInterface[]
   */
  public function getQuizArchived() {
    $tid = FALSE;
    if (isset($_GET['tags'])) {
      $tags = $_GET['tags'];
      $tid = $this->getTagsTid($tags);
    }
    $now = new DrupalDateTime('now');
    if ($tid) {
      $ids = \Drupal::entityQuery('quiz')
        ->condition('status', 1)
        ->condition('field_category', $tid)
        ->condition('field_expired', $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '<')
        ->sort('field_weight', 'ASC')
        ->sort('created', 'DESC')
        ->pager(15)
        ->execute();
    }
    elseif (isset($_GET['keys'])) {
      $ids = \Drupal::entityQuery('quiz')
        ->condition('status', 1)
        ->condition('name', $_GET['keys'], 'CONTAINS')
        ->condition('field_expired', $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '<')
        ->sort('field_weight', 'ASC')
        ->sort('created', 'DESC')
        ->pager(15)
        ->execute();
    }
    else {
      $ids = \Drupal::entityQuery('quiz')
        ->condition('status', 1)
        ->condition('field_expired', $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '<')
        ->sort('field_weight', 'ASC')
        ->sort('created', 'DESC')
        ->pager(15)
        ->execute();
    }
    $result = \Drupal\cme_quiz\Entity\Quiz::loadMultiple($ids);
    foreach ($result as $r) {

      $r->author = $this->getAuthor($r->getOwnerId());
      if ($this->checkExpiredQuiz($r)) {
        $r->expired = TRUE;
      }
      else {
        $r->expired = FALSE;
      }
      if ($this->checkQuizStatusUser($r->id())) {
        $r->check_event = TRUE;
        if ($this->checkUserAttempStatus($r)) {
          $r->attemp = 'Passed';
        }
        else {
          $r->attemp = 'Attempted';
        }
      }
      else {
        $r->check_event = FALSE;
      }
    }
    return $result;
  }

  /**
   * @return array
   */
  public function getTags() {
    $tags = [];
    $ids = \Drupal::entityQuery('quiz')->condition('status', 1)->execute();
    $result = \Drupal\cme_quiz\Entity\Quiz::loadMultiple($ids);
    foreach ($result as $drug) {
      foreach ($drug->get('field_category')->getValue() as $tag) {
        $term = \Drupal\taxonomy\Entity\Term::load($tag['target_id']);
        $tags[$tag['target_id']] = $term->getName();
      }
    }
    return $tags;
  }

  /**
   * @param $name
   *
   * @return int|string|null
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getTagsTid($name) {
    $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties([
        'name' => $name,
        'vid' => 'quiz',
      ]);
    $term = reset($term);
    $term_id = $term->id();
    return $term_id;
  }


  /**
   * get product by Event
   */
  public function getQuestions($quiz_id) {
    $ids = \Drupal::entityQuery('question')
      ->condition('type', 'default')
      ->condition('status', 1)
      ->condition('field_quiz', $quiz_id)
      ->execute();
    $results = \Drupal\cme_question\Entity\Question::loadMultiple($ids);
    $result = reset($results);
    return $result;
  }

  /**
   * @param $quiz_id
   *
   * @return bool|null
   */
  public function checkQuizStatusUser($quiz_id) {
    $return = NULL;
    $user = \Drupal::currentUser();
    $uid = $user->id();
    $ids = \Drupal::entityQuery('result')
      ->condition('status', 1)
      ->condition('field_quiz', $quiz_id)
      ->condition('field_user', $uid)
      ->execute();
    $results = \Drupal\cme_result\Entity\Result::loadMultiple($ids);
    if ($results) {
      $return = TRUE;
    }
    return $return;
  }

  /**
   * @param $quiz
   *
   * @return bool
   */
  public function checkExpiredQuiz($quiz) {
    $current = time();
    $date = strtotime($quiz->get('field_expired')->value);
    if ($current > $date) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * @param $quiz
   *
   * @return bool|null
   */
  public function checkUserAttempStatus($quiz) {
    $return = NULL;
    $user = \Drupal::currentUser();
    $uid = $user->id();
    $ids = \Drupal::entityQuery('result')
      ->condition('status', 1)
      ->condition('field_quiz', $quiz->id())
      ->condition('field_user', $uid)
      ->execute();
    $results = \Drupal\cme_result\Entity\Result::loadMultiple($ids);
    if ($results) {
      foreach ($results as $result) {
        if ($result->get('field_passed')->value == 1) {
          $return = TRUE;
          break;
        }
      }
    }
    return $return;
  }

  /**
   * @param $uid
   *
   * @return string
   */
  public function getAuthor($uid) {

    $user = \Drupal\user\Entity\User::load($uid);
    $account = $user->toArray();
    $name = '';
    if ($account['field_first_name']) {
      $name .= $account['field_first_name'][0]['value'];
    }
    if ($account['field_last_name']) {
      $name .= ' ' . $account['field_last_name'][0]['value'];
    }
    if ($name == '') {
      $name .= $account['name'][0]['value'];
    }
    return $name;
  }

  /**
   * @return array
   */
  public function title() {
    return [
      '#markup' => \Drupal::state()->get('/cme/quiz', 'CME Quizzes'),
      '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList(),
    ];
  }

  /**
   * @return array
   */
  public function titleArchived() {
    return [
      '#markup' => \Drupal::state()->get('/cme/quiz/archived', 'Archived Quizzes'),
      '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList(),
    ];
  }
  public function quiz_archived($id){
    $quiz = \Drupal\cme_quiz\Entity\Quiz::load($id);
    if (strtotime($quiz->get('field_expired')->value) >= time()) {
      \Drupal::messenger()->addMessage('The quiz '.$quiz->get('name')->value.' is not archived.', 'error');
      $response = new RedirectResponse('/cme/quiz/');
      $response->send();
    }
    return $this->getQuestionAnswered($quiz);
  }

  /**
   * @return array
   */
  public function titleArchivedDetail($id) {
    $quiz = \Drupal\cme_quiz\Entity\Quiz::load($id);
    return [
      '#markup' => $quiz->get('name')->value,
      '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList(),
    ];
  }

  /**
   * @param $quiz
   *
   * @return array
   */
  public function getQuestionAnswered($quiz) {
    $service = \Drupal::service('cme_question.question');
    $return = [];
    $questions = getQuestions($quiz->id());
    foreach ($questions as $question) {
      $return[] = ['question' => $question, 'type' => $service->getQuestionArchived($question)];
    }

    $theme = [
      '#theme' => 'cme_quiz_question_archived',
      '#quiz' => $quiz,
      '#questions' => $return,
    ];
    return $theme;
  }
}
