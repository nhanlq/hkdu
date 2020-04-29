<?php

namespace Drupal\clinical_focus\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Clinical focus revision.
 *
 * @ingroup clinical_focus
 */
class ClinicalFocusRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The Clinical focus revision.
   *
   * @var \Drupal\clinical_focus\Entity\ClinicalFocusInterface
   */
  protected $revision;

  /**
   * The Clinical focus storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $clinicalFocusStorage;

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
    $instance->clinicalFocusStorage = $container->get('entity_type.manager')->getStorage('clinical_focus');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'clinical_focus_revision_delete_confirm';
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
    return new Url('entity.clinical_focus.version_history', ['clinical_focus' => $this->revision->id()]);
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
  public function buildForm(array $form, FormStateInterface $form_state, $clinical_focus_revision = NULL) {
    $this->revision = $this->ClinicalFocusStorage->loadRevision($clinical_focus_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->ClinicalFocusStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Clinical focus: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of Clinical focus %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.clinical_focus.canonical',
       ['clinical_focus' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {clinical_focus_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.clinical_focus.version_history',
         ['clinical_focus' => $this->revision->id()]
      );
    }
  }

}
