<?php

namespace Drupal\cme_quiz\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class QuizResultController.
 */
class QuizResultController extends ControllerBase {

  /**
   * Test_result.
   *
   * @return string
   *   Return Hello string.
   */
  public function test_result($quizId, $resultId) {
    $quiz = \Drupal\cme_quiz\Entity\Quiz::load($quizId);
    return getQuestionAnswered($quiz, $resultId);
  }

}
