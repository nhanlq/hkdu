<?php

namespace Drupal\cme_event\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EventCalendarController.
 */
class EventCalendarController extends ControllerBase
{

    /**
     * Calendar.
     *
     * @return string
     *   Return Hello string.
     */
    public function calendar()
    {
        $event = $this->getCMEEvent();
        $quiz = $this->getQuiz();
        $data = array_merge($event,$quiz);
        $response = new Response();
        $data = $response->setContent(json_encode($data));

        $date = date('Y-m-d');
        if($event || $quiz){
          return [
            '#theme' => 'cme_event_calendar',
            '#events' => $data->getContent(),
            '#date' => $date
          ];
        }
        else{
          return [
            '#markup' => 'There is no event or quiz.'
          ];
        }

    }

    /**
     * Get CME Event
     */
    public function getCMEEvent(){
        $currentDate = time();
        $ids = \Drupal::entityQuery('cme_event')
            ->condition('status', 1)
            ->condition('field_expired',$currentDate,'>=')
            ->sort('field_weight','ASC')
            ->sort('created','DESC')
            ->execute();
        $result = \Drupal\cme_event\Entity\CmeEvent::loadMultiple($ids);
        $data = [];

        foreach($result as $key => $event){
            $event_obj = new \stdClass();
            $event_obj->title = $event->getName();
            $event_obj->start = $event->get('field_start_date')->value;
            $time = str_replace('-','',$event->get('field_start_date')->value).'T'.str_replace(':','',$event->get('field_start_time')->value).'/'.str_replace('-','',$event->get('field_date')->value).'T'.str_replace(':','',$event->get('field_end_time')->value);
            $options = ['absolute' => TRUE];

            $url_object = \Drupal\Core\Url::fromRoute('entity.cme_event.canonical', ['cme_event' => $key], $options)->toString();
            $event_obj->url = 'https://calendar.google.com/calendar/u/0/r/eventedit?text='.$event->getName().'&dates='.$time.'&details=For+details,+link+here:+'.$url_object.'&location='.$event->get('field_location')->value.'&sf=true&output=xml';
            $data[] = $event_obj;

        }
        return $data;
    }

    /**
     * get Quiz
     */
    public function getQuiz(){
        $currentDate = time();
        $ids = \Drupal::entityQuery('quiz')
            ->condition('status', 1)
            ->condition('field_expired',$currentDate,'>=')
            ->sort('field_weight','ASC')
            ->sort('created','DESC')
            ->execute();
        $data = [];
        $result = \Drupal\cme_quiz\Entity\Quiz::loadMultiple($ids);
        foreach($result as $key => $quiz){
            $event_obj = new \stdClass();
            $event_obj->title = $quiz->getName();
            $date = '';
            if(isset($quiz->get('field_lecture_date')->value)){
                $date = $quiz->get('field_lecture_date')->value;
            }else{
                $date = $quiz->get('field_start_date')->value;
            }
            $event_obj->start = $date;

            if(isset($quiz->get('field_lecture_time')->value)){
                $lecture_time = str_replace(':','',$quiz->get('field_lecture_time')->value);
            }else{
                $lecture_time  = str_replace(':','',$quiz->get
                ('field_start_time')->value);
            }

            $time = str_replace('-','',$date).'T'.$lecture_time.'/'
              .str_replace('-','',$quiz->get('field_end_date')->value).'T'
              .str_replace(':','',$quiz->get('field_end_time')->value);
            $options = ['absolute' => TRUE];

            $url_object = \Drupal\Core\Url::fromRoute('entity.quiz.canonical', ['quiz' => $key], $options)->toString();
            $event_obj->url = 'https://calendar.google.com/calendar/u/0/r/eventedit?text='.$quiz->getName().'&dates='.$time.'&details=For+details,+link+here:+'.$url_object.'&sf=true&output=xml';
            $data[] = $event_obj;
        }
        return $data;
    }

}
