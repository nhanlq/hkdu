<?php

namespace Drupal\hospital\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Hospital entity revision.
 *
 * @ingroup hospital
 */
class HospitalEntityRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The Hospital entity revision.
   *
   * @var \Drupal\hospital\Entity\HospitalEntityInterface
   */
  protected $revision;

  /**
   * The Hospital entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $hospitalEntityStorage;

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
    $instance->hospitalEntityStorage = $container->get('entity_type.manager')->getStorage('hospital');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'hospital_revision_delete_confirm';
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
    return new Url('entity.hospital.version_history', ['hospital' => $this->revision->id()]);
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
  public function buildForm(array $form, FormStateInterface $form_state, $hospital_revision = NULL) {
    $this->revision = $this->HospitalEntityStorage->loadRevision($hospital_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->HospitalEntityStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Hospital entity: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of Hospital entity %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.hospital.canonical',
       ['hospital' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {hospital_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.hospital.version_history',
         ['hospital' => $this->revision->id()]
      );
    }
  }

}
