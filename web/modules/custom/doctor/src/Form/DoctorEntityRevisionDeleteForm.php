<?php

namespace Drupal\doctor\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Doctor entity revision.
 *
 * @ingroup doctor
 */
class DoctorEntityRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The Doctor entity revision.
   *
   * @var \Drupal\doctor\Entity\DoctorEntityInterface
   */
  protected $revision;

  /**
   * The Doctor entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $doctorEntityStorage;

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
    $instance->doctorEntityStorage = $container->get('entity_type.manager')->getStorage('doctor');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'doctor_revision_delete_confirm';
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
    return new Url('entity.doctor.version_history', ['doctor' => $this->revision->id()]);
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
  public function buildForm(array $form, FormStateInterface $form_state, $doctor_revision = NULL) {
    $this->revision = $this->DoctorEntityStorage->loadRevision($doctor_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->DoctorEntityStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Doctor entity: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of Doctor entity %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.doctor.canonical',
       ['doctor' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {doctor_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.doctor.version_history',
         ['doctor' => $this->revision->id()]
      );
    }
  }

}
