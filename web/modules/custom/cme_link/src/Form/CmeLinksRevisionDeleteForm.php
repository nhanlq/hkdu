<?php

namespace Drupal\cme_link\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a CME Links revision.
 *
 * @ingroup cme_link
 */
class CmeLinksRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The CME Links revision.
   *
   * @var \Drupal\cme_link\Entity\CmeLinksInterface
   */
  protected $revision;

  /**
   * The CME Links storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $cmeLinksStorage;

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
    $instance->cmeLinksStorage = $container->get('entity_type.manager')->getStorage('cme_links');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cme_links_revision_delete_confirm';
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
    return new Url('entity.cme_links.version_history', ['cme_links' => $this->revision->id()]);
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
  public function buildForm(array $form, FormStateInterface $form_state, $cme_links_revision = NULL) {
    $this->revision = $this->CmeLinksStorage->loadRevision($cme_links_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->CmeLinksStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('CME Links: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of CME Links %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.cme_links.canonical',
       ['cme_links' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {cme_links_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.cme_links.version_history',
         ['cme_links' => $this->revision->id()]
      );
    }
  }

}
