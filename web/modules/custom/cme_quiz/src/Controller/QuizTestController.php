<?php

namespace Drupal\cme_quiz\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Executable\ExecutableException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class QuizTestController.
 */
class QuizTestController extends ControllerBase
{

    /**
     * Quiz_test.
     *
     * @return string
     *   Return Hello string.
     */
    public function quiz_test($quizId)
    {
        $user = \Drupal::currentUser();
        $quiz = \Drupal\cme_quiz\Entity\Quiz::load($quizId);
        $post = $_POST;
        $questions = getQuestions($quizId);
        $tracking = [];
        $passing_percent = $quiz->get('field_percent')->value;
        $total_question = count($questions);
        $total_correct = 0;
        $total_percent = 0;

        if(count($post) <= 1){
            \Drupal::messenger()->addMessage( 'Please choose answer before submit the test.','error');
            $response = new RedirectResponse('/cme/quiz/'.$quizId);
            $response->send();
        }
        foreach ($questions as $question) {
            $para_id = '';
            $correct = 0;
            switch ($question->get('field_question_type')->value) {
                case 'true_false':
                    $value = $post['truefalse_' . $question->id()];
                    $val = explode('_', $value);
                    $answer = $val[0] == 'true' ? 'True' : 'False';

                    //get correct answer
                     $mapp = '';
                    foreach ($question->get('field_true_false')->getValue() as $id) {
                        $true = \Drupal\paragraphs\Entity\Paragraph::load($id['target_id']);
                        $mapp = $true->get('field_true_false')->value;
                    }
                    if($val[0]=='true' && $mapp=='True' || $val[0]=='false' && $mapp=='False'){
                        $correct = 1;
                    }
                    break;
                case 'single':
                    $value = $post['single_' . $question->id()];
                    $an = \Drupal\paragraphs\Entity\Paragraph::load($value);
                    $answer = $an->get('field_answer')->value;
                    //get correct answer
                        if ($an->get('field_correct_answer')->value == 1) {
                            $correct = 1;
                        }
                    $para_id = $value;
                    break;
                case 'multiple':
                    $value = $post['multiple_' . $question->id()];
                    $ans = [];
                    foreach($value as $val){
                        $an = \Drupal\paragraphs\Entity\Paragraph::load($val);
                        $ans[]= $an->get('field_answer')->value;
                    }
                    $default = $question->get('field_multiple_choice')->getValue();
                    $default_correct = [];
                    foreach($default as $de){
                        $paramu = \Drupal\paragraphs\Entity\Paragraph::load($de['target_id']);
                        if($paramu->get('field_correct_answer')->value == 1){
                            $default_correct[] = $paramu->id();
                        }
                    }
                    $compare = array_diff($value,$default_correct);
                    if(!$compare){
                      $correct = 1;
                    }
                    $para_id = implode(',',$value);
                    $answer = implode(', ',$ans);
                    //get correct answer

                    break;
            }
            $correct_answer = 0;

            if ($correct == 1) {
                $correct_answer = 1;
                $total_correct+= 1;
            }
            $paragraph  = \Drupal\paragraphs\Entity\Paragraph::create([
                'type' => 'result',
                'field_answer' => $answer,
                'field_correct_answer' => $correct_answer,
                'field_question' => $question->id(),
                'field_paragraph_id' => $para_id,
            ]);
            try {
                $paragraph->save();
            }catch (Exception $e){
                throw new \RuntimeException($e->getMessage());
            }
            $tracking[] = ['target_id'=>$paragraph->id(),'target_revision_id' => $paragraph->getRevisionId()];
        }
        $total_percent = $total_correct * 100/$total_question;
        $pass = 0;
        if($total_percent >= $passing_percent){
            $pass = 1;
        }
        //add score to quiz
        $score = 0;
        if($pass==1){
            $score = $quiz->get('field_point')->value;
        }
        $result = \Drupal\cme_result\Entity\Result::create([
            'name' => 'Result of ' . $quiz->getName() . ' - User ' . $user->getAccountName(),
            'create' => time(),
            'changed' => time(),
            'status' => 1,
            'field_quiz' => $quizId,
            'field_user' => $user->id(),
            'field_result' => $tracking,
            'field_percent' => $total_percent,
            'field_passed' => $pass,
            'uid' => $user->id(),
            'field_score' => $score,
        ]);
        try {
            $result->save();

            $this->CreateQuizScore($quizId, $score);

            $response = new RedirectResponse('/cme/quiz/'.$quizId.'/result/'.$result->id());
            $response->send();
        }catch(Exception $e){
            throw new \RuntimeException($e->getMessage());
        }


        return [
            '#type' => 'markup',
            '#markup' => $this->t($quizId)
        ];
    }

    //check quiz score
    public function CreateQuizScore($quizId, $score){
        $user = \Drupal::currentUser();
        $account = \Drupal\user\Entity\User::load($user->id());
        $quiz = \Drupal\cme_quiz\Entity\Quiz::load($quizId);
        $ids = \Drupal::entityQuery('score')
            ->condition('status', 1)
            ->condition('field_quiz', $quizId)
            ->condition('field_user', $user->id())
            ->execute();
        $results = \Drupal\cme_score\Entity\Score ::loadMultiple($ids);
        if($results){
            $return = reset($results);
            if($exist = $this->getHighestScore($quizId, $user)){
                $highest = reset($exist);
                $highest_score = $highest->get('field_score')->value;
                if($highest_score < $score){
                    $return->set('field_score',$score);
                    $account->set('field_point',($account->get('field_point')->value - $highest_score) + $score);
                    $account->save();
                }
            }
            $return->save();

        }else{
            $return = \Drupal\cme_score\Entity\Score::create([
                'status' =>1,
                'uid' => $user->id(),
                'field_quiz' => $quizId,
                'field_user' => $user->id(),
                'field_date' => date('Y-m-d',time()),
                'field_attendance' => 1,
                'field_organizer' => $quiz->get('field_organiser')->value,
                'field_score' => $score,
                'name' => 'User '.$user->getDisplayName().' of Quiz '.$quizId,
                'created' => time(),
                'changed' => time(),
            ]);
            $return->save();
            $account->set('field_point',$account->get('field_point')->value + $score);
            $account->save();
        }
        return $return;
    }

    public function getHighestScore($quizId, $user = null){
      if(!$user){
          $user = \Drupal::currentUser();
      }
      $ids = \Drupal::entityQuery('score')
          ->condition('status', 1)
          ->condition('field_quiz', $quizId)
          ->condition('field_user', $user->id())
          ->sort('field_score','DESC')
          ->range(0,1)
          ->execute();
      $result = \Drupal\cme_score\Entity\Score::loadMultiple($ids);
      if($result){
        return $result;
      }else{
          return false;
      }
    }

}
