<?php

namespace Drupal\quiz_tracking\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Quiz tracking revision.
 *
 * @ingroup quiz_tracking
 */
class QuizTrackingRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The Quiz tracking revision.
   *
   * @var \Drupal\quiz_tracking\Entity\QuizTrackingInterface
   */
  protected $revision;

  /**
   * The Quiz tracking storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $quizTrackingStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->quizTrackingStorage = $container->get('entity_type.manager')->getStorage('quiz_tracking');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'quiz_tracking_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the revision from %revision-date?', [
      '%revision-date' => format_date($this->revision->getRevisionCreationTime()),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.quiz_tracking.version_history', ['quiz_tracking' => $this->revision->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $quiz_tracking_revision = NULL) {
    $this->revision = $this->QuizTrackingStorage->loadRevision($quiz_tracking_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->QuizTrackingStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Quiz tracking: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of Quiz tracking %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.quiz_tracking.canonical',
       ['quiz_tracking' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {quiz_tracking_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.quiz_tracking.version_history',
         ['quiz_tracking' => $this->revision->id()]
      );
    }
  }

}
