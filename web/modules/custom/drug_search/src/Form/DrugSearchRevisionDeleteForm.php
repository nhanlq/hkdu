<?php

namespace Drupal\drug_search\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Drug search revision.
 *
 * @ingroup drug_search
 */
class DrugSearchRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The Drug search revision.
   *
   * @var \Drupal\drug_search\Entity\DrugSearchInterface
   */
  protected $revision;

  /**
   * The Drug search storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $drugSearchStorage;

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
    $instance->drugSearchStorage = $container->get('entity_type.manager')->getStorage('drug_search');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'drug_search_revision_delete_confirm';
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
    return new Url('entity.drug_search.version_history', ['drug_search' => $this->revision->id()]);
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
  public function buildForm(array $form, FormStateInterface $form_state, $drug_search_revision = NULL) {
    $this->revision = $this->DrugSearchStorage->loadRevision($drug_search_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->DrugSearchStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Drug search: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of Drug search %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.drug_search.canonical',
       ['drug_search' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {drug_search_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.drug_search.version_history',
         ['drug_search' => $this->revision->id()]
      );
    }
  }

}
