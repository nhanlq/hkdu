<?php

namespace Drupal\event_tracking\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Event tracking revision.
 *
 * @ingroup event_tracking
 */
class EventTrackingRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The Event tracking revision.
   *
   * @var \Drupal\event_tracking\Entity\EventTrackingInterface
   */
  protected $revision;

  /**
   * The Event tracking storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $eventTrackingStorage;

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
    $instance->eventTrackingStorage = $container->get('entity_type.manager')->getStorage('event_tracking');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'event_tracking_revision_delete_confirm';
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
    return new Url('entity.event_tracking.version_history', ['event_tracking' => $this->revision->id()]);
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
  public function buildForm(array $form, FormStateInterface $form_state, $event_tracking_revision = NULL) {
    $this->revision = $this->EventTrackingStorage->loadRevision($event_tracking_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->EventTrackingStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Event tracking: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of Event tracking %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.event_tracking.canonical',
       ['event_tracking' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {event_tracking_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.event_tracking.version_history',
         ['event_tracking' => $this->revision->id()]
      );
    }
  }

}
