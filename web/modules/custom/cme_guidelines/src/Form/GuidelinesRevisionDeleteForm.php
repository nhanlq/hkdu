<?php

namespace Drupal\cme_guidelines\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Guidelines revision.
 *
 * @ingroup cme_guidelines
 */
class GuidelinesRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The Guidelines revision.
   *
   * @var \Drupal\cme_guidelines\Entity\GuidelinesInterface
   */
  protected $revision;

  /**
   * The Guidelines storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $guidelinesStorage;

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
    $instance->guidelinesStorage = $container->get('entity_type.manager')->getStorage('guidelines');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'guidelines_revision_delete_confirm';
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
    return new Url('entity.guidelines.version_history', ['guidelines' => $this->revision->id()]);
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
  public function buildForm(array $form, FormStateInterface $form_state, $guidelines_revision = NULL) {
    $this->revision = $this->GuidelinesStorage->loadRevision($guidelines_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->GuidelinesStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Guidelines: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of Guidelines %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.guidelines.canonical',
       ['guidelines' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {guidelines_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.guidelines.version_history',
         ['guidelines' => $this->revision->id()]
      );
    }
  }

}
