<?php

namespace Drupal\pharm_dir\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Pharm dir revision.
 *
 * @ingroup pharm_dir
 */
class PharmDirRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The Pharm dir revision.
   *
   * @var \Drupal\pharm_dir\Entity\PharmDirInterface
   */
  protected $revision;

  /**
   * The Pharm dir storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $pharmDirStorage;

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
    $instance->pharmDirStorage = $container->get('entity_type.manager')->getStorage('pharm_dir');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pharm_dir_revision_delete_confirm';
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
    return new Url('entity.pharm_dir.version_history', ['pharm_dir' => $this->revision->id()]);
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
  public function buildForm(array $form, FormStateInterface $form_state, $pharm_dir_revision = NULL) {
    $this->revision = $this->PharmDirStorage->loadRevision($pharm_dir_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->PharmDirStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Pharm dir: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of Pharm dir %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.pharm_dir.canonical',
       ['pharm_dir' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {pharm_dir_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.pharm_dir.version_history',
         ['pharm_dir' => $this->revision->id()]
      );
    }
  }

}
