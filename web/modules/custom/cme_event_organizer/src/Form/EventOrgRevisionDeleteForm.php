<?php

namespace Drupal\cme_event_organizer\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Event Organizer revision.
 *
 * @ingroup cme_event_organizer
 */
class EventOrgRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The Event Organizer revision.
   *
   * @var \Drupal\cme_event_organizer\Entity\EventOrgInterface
   */
  protected $revision;

  /**
   * The Event Organizer storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $eventOrgStorage;

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
    $instance->eventOrgStorage = $container->get('entity_type.manager')->getStorage('event_org');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'event_org_revision_delete_confirm';
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
    return new Url('entity.event_org.version_history', ['event_org' => $this->revision->id()]);
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
  public function buildForm(array $form, FormStateInterface $form_state, $event_org_revision = NULL) {
    $this->revision = $this->EventOrgStorage->loadRevision($event_org_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->EventOrgStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Event Organizer: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of Event Organizer %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.event_org.canonical',
       ['event_org' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {event_org_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.event_org.version_history',
         ['event_org' => $this->revision->id()]
      );
    }
  }

}
