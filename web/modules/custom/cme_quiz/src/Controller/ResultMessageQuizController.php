<?php

namespace Drupal\cme_quiz\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class ResultMessageQuizController.
 */
class ResultMessageQuizController extends ControllerBase {

  /**
   * Test_message.
   *
   * @return string
   *   Return Hello string.
   */
  public function test_message($id) {
      $message = \Drupal::state()->get('quiz_message_done');
      $message = str_replace('[id]',$id,$message);
      return [
              '#theme' => 'cme_quiz_message',
              '#message' => $message,
      ];
  }

}
