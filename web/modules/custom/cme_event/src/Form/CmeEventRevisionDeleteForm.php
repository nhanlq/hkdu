<?php

namespace Drupal\cme_event\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a CME Event revision.
 *
 * @ingroup cme_event
 */
class CmeEventRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The CME Event revision.
   *
   * @var \Drupal\cme_event\Entity\CmeEventInterface
   */
  protected $revision;

  /**
   * The CME Event storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $cmeEventStorage;

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
    $instance->cmeEventStorage = $container->get('entity_type.manager')->getStorage('cme_event');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cme_event_revision_delete_confirm';
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
    return new Url('entity.cme_event.version_history', ['cme_event' => $this->revision->id()]);
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
  public function buildForm(array $form, FormStateInterface $form_state, $cme_event_revision = NULL) {
    $this->revision = $this->CmeEventStorage->loadRevision($cme_event_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->CmeEventStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('CME Event: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of CME Event %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.cme_event.canonical',
       ['cme_event' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {cme_event_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.cme_event.version_history',
         ['cme_event' => $this->revision->id()]
      );
    }
  }

}
