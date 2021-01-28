<?php
/**
 * Created by PhpStorm.
 * User: nhanl
 * Date: 4/10/2020
 * Time: 11:39 AM
 */

namespace Drupal\cme_question;


use phpDocumentor\Reflection\Types\Mixed_;

class Question
{
    /**
     * Get question type
     * @param $question
     * @return Mixed
     */
    public function getQuestion($question)
    {
        switch ($question->get('field_question_type')->value) {
            case 'true_false':
                $output = $this->trueFalse($question);
                break;
            case 'single':
                $output = $this->singleChoice($question);
                break;
            case 'multiple':
                $output = $this->multipleChoice($question);
                break;
            default:
                $output = false;
        }
        return $output;
    }

    /**
     * Single
     */
    public function singleChoice($question)
    {
        $output = '';
        foreach ($question->get('field_single_choice')->getValue() as $single) {
            $para = \Drupal\paragraphs\Entity\Paragraph::load($single['target_id']);
            $output .= ' <label display="inline-flex" class="bjjYsL">';
            $output .= '<span class="gSrgng">';
            $output .= '<input type="radio" id="single-' . $para->id() . '" class="psDju" name="single_' . $question->id() . '" value="' . $para->id() . '">';
            $output .= '<span class="gsdkyl"></span>';
            $output .= '</span>';
            $output .= '<span class="fVCIOT"></span><span class="hSMymM">' . $para->get('field_answer')->value . '</span></span>';
            $output .= '</label>';
        }
        return $output;
    }

    /**
     * Single
     */
    public function multipleChoice($question)
    {
        $output = '';
        foreach ($question->get('field_multiple_choice')->getValue() as $multiple) {
            $para = \Drupal\paragraphs\Entity\Paragraph::load($multiple['target_id']);
            $output .= ' <label display="inline-flex" class="bjjYsL">';
            $output .= '<span class="gSrgng">';
            $output .= '<input type="checkbox" id="multiple-' . $para->id() . '" class="psDju" name="multiple_' . $question->id() . '[]" value="' . $para->id() . '">';
            $output .= '<span class="gsdkyl"></span>';
            $output .= '</span>';
            $output .= '<span class="fVCIOT"></span><span class="hSMymM">' . $para->get('field_answer')->value . '</span></span>';
            $output .= '</label>';
            //    $output .= '<input type="checkbox" id="multiple-' . $para->id() . '" name="multiple-' . $question->id() . '" value="' . $question->id() . '-' . $para->id() . '"><label for="multiple-' . $para->id() . '">' . $para->get('field_answer')->value . '</label>';
        }
        return $output;
    }

    public function trueFalse($question)
    {
        $output = '';
        $output .= ' <label display="inline-flex" class="bjjYsL">';
        $output .= '<span class="gSrgng">';
        $output .= '<input type="radio" id="true-' . $question->id() . '" class="psDju" name="truefalse_' . $question->id() . '" value="true_' . $question->id() . '">';
        $output .= '<span class="gsdkyl"></span>';
        $output .= '</span>';
        $output .= '<span class="fVCIOT"></span><span class="hSMymM">True</span></span>';
        $output .= '</label>';

        $output .= ' <label display="inline-flex" class="bjjYsL">';
        $output .= '<span class="gSrgng">';
        $output .= '<input type="radio" id="false-' . $question->id() . '" class="psDju" name="truefalse_' . $question->id() . '" value="false_' . $question->id() . '">';
        $output .= '<span class="gsdkyl"></span>';
        $output .= '</span>';
        $output .= '<span class="fVCIOT"></span><span class="hSMymM">False</span></span>';
        $output .= '</label>';
        return $output;
    }

    public function getQuestionAnswered($question, $quiz, $resultId = null)
    {
        switch ($question->get('field_question_type')->value) {
            case 'true_false':
                $output = $this->trueFalseAnswered($question, $quiz, $resultId);
                break;
            case 'single':
                $output = $this->singleChoiceAnswered($question, $quiz, $resultId);
                break;
            case 'multiple':
                $output = $this->multipleChoiceAnswered($question, $quiz, $resultId);
                break;
            default:
                $output = false;
        }
        return $output;
    }

    /**
     * Single
     */
    public function singleChoiceAnswered($question, $quiz, $resultId)
    {
        $result = $this->getResultQuestion($quiz, $resultId);
        $target_result = $result->get('field_result')->getValue();
        $parac = false;
        foreach ($target_result as $r) {
            $target = \Drupal\paragraphs\Entity\Paragraph::load($r['target_id']);
            if ($question->id() == $target->get('field_question')->target_id) {
                $parac = $target->get('field_paragraph_id')->value;
            }
        }
        $output = '';
        foreach ($question->get('field_single_choice')->getValue() as $single) {
            $para = \Drupal\paragraphs\Entity\Paragraph::load($single['target_id']);
            $check = '';
            if ($parac == $single['target_id']) {
                $check = 'checked="checked"';
            }
            $output .= ' <label display="inline-flex" class="bjjYsL">';
            $output .= '<span class="gSrgng">';
            $output .= '<input disabled="disabled" ' . $check . ' type="radio" id="single-' . $para->id() . '" class="psDju" name="single_' . $question->id() . '" value="' . $para->id() . '">';
            $output .= '<span class="gsdkyl"></span>';
            $output .= '</span>';
            if ($para->get('field_correct_answer')->value == 1) {
                $output .= '<span class="fVCIOT"></span><span class="hSMymM blue">' . $para->get('field_answer')->value . '</span></span>';
            } else {
                $output .= '<span class="fVCIOT"></span><span class="hSMymM">' . $para->get('field_answer')->value . '</span></span>';
            }

            $output .= '</label>';
        }
        return $output;
    }

    /**
     * Single
     */
    public function multipleChoiceAnswered($question, $quiz, $resultId)
    {
        $result = $this->getResultQuestion($quiz, $resultId);
        $target_result = $result->get('field_result')->getValue();

        $parac = false;

        $output = '';
        foreach ($question->get('field_multiple_choice')->getValue() as $multiple) {
            $para = \Drupal\paragraphs\Entity\Paragraph::load($multiple['target_id']);

            $output .= ' <label display="inline-flex" class="bjjYsL">';
            $output .= '<span class="gSrgng">';
            $right = [];
            foreach ($target_result as $r) {
                $target = \Drupal\paragraphs\Entity\Paragraph::load($r['target_id']);
                if ($question->id() == $target->get('field_question')->target_id) {
                    $parac = $target->get('field_paragraph_id')->value;
                    $parac = explode(',', $parac);
                    break;
                }
            }
            if (in_array($multiple['target_id'], $parac)) {
                $output .= '<input checked="checked" disabled="disabled" type="checkbox" id="multiple-' . $para->id() . '" class="psDju" name="multiple_' . $question->id() . '[]" value="' . $para->id() . '">';
            } else {
                $output .= '<input disabled="disabled" type="checkbox" id="multiple-' . $para->id() . '" class="psDju" name="multiple_' . $question->id() . '[]" value="' . $para->id() . '">';
            }
            $output .= implode(' ', $right);
            $output .= '<span class="gsdkyl"></span>';
            $output .= '</span>';
            if ($para->get('field_correct_answer')->value == 1) {
                $output .= '<span class="fVCIOT"></span><span class="hSMymM blue">' . $para->get('field_answer')->value . '</span></span>';
            } else {
                $output .= '<span class="fVCIOT"></span><span class="hSMymM">' . $para->get('field_answer')->value . '</span></span>';
            }

            $output .= '</label>';
            //    $output .= '<input type="checkbox" id="multiple-' . $para->id() . '" name="multiple-' . $question->id() . '" value="' . $question->id() . '-' . $para->id() . '"><label for="multiple-' . $para->id() . '">' . $para->get('field_answer')->value . '</label>';
        }
        return $output;
    }

    public function trueFalseAnswered($question, $quiz, $resultId)
    {
        //get correct
        $result = $this->getResultQuestion($quiz, $resultId);
        $target_result = $result->get('field_result')->getValue();
        $answer = '';
        foreach ($target_result as $r) {
            $res = \Drupal\paragraphs\Entity\Paragraph::load($r['target_id']);
            if ($res->get('field_question')->target_id == $question->id()) {
                $answer = $res->get('field_answer')->value;
                break;
            }
        }
        $true = '';
        $false = '';
        if ($answer == 'True') {
            $true = 'checked="checked"';
        }
        if ($answer == 'False') {
            $false = 'checked="checked"';
        }
        $truefalse = $question->get('field_true_false')->getValue();
        foreach ($truefalse as $id) {
            $para = \Drupal\paragraphs\Entity\Paragraph::load($id['target_id']);
            $tf = $para->get('field_true_false')->value;
        }
        $output = '';
        $output .= ' <label display="inline-flex" class="bjjYsL">';
        $output .= '<span class="gSrgng">';
        $output .= '<input disabled="disabled" ' . $true . ' type="radio" id="true-' . $question->id() . '" class="psDju" name="truefalse_' . $question->id() . '" value="true_' . $question->id() . '">';
        $output .= '<span class="gsdkyl"></span>';
        $output .= '</span>';
        if ($tf == 'True') {
            $output .= '<span class="fVCIOT"></span><span class="hSMymM blue">True</span></span>';
        } else {
            $output .= '<span class="fVCIOT"></span><span class="hSMymM">True</span></span>';
        }
        $output .= '</label>';
        $output .= ' <label display="inline-flex" class="bjjYsL">';
        $output .= '<span class="gSrgng">';
        $output .= '<input disabled="disabled" ' . $false . ' type="radio" id="false-' . $question->id() . '" class="psDju" name="truefalse_' . $question->id() . '" value="false_' . $question->id() . '">';
        $output .= '<span class="gsdkyl"></span>';
        $output .= '</span>';
        if ($tf == 'False') {
            $output .= '<span class="fVCIOT"></span><span class="hSMymM blue">False</span></span>';
        } else {
            $output .= '<span class="fVCIOT"></span><span class="hSMymM">False</span></span>';
        }
        $output .= '</label>';
        return $output;
    }

    public function getResultQuestion($quiz, $resultId = null)
    {
        if ($resultId) {
            $result = \Drupal\cme_result\Entity\Result::load($resultId);
            return $result;
        } else {
            $user = \Drupal::currentUser();
            $id = \Drupal::entityQuery('result')
                ->condition('status', 1)
                ->condition('field_user', $user->id())
                ->condition('field_quiz', $quiz->id())
                ->sort('field_percent', 'DESC')
                ->range(0, 1)
                ->execute();
            $result = \Drupal\cme_result\Entity\Result::loadMultiple($id);
            $result = reset($result);
            return $result;
        }

    }

}