<?php

namespace Drupal\drug_news\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Drug news revision.
 *
 * @ingroup drug_news
 */
class DrugNewsRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The Drug news revision.
   *
   * @var \Drupal\drug_news\Entity\DrugNewsInterface
   */
  protected $revision;

  /**
   * The Drug news storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $drugNewsStorage;

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
    $instance->drugNewsStorage = $container->get('entity_type.manager')->getStorage('drug_news');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'drug_news_revision_delete_confirm';
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
    return new Url('entity.drug_news.version_history', ['drug_news' => $this->revision->id()]);
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
  public function buildForm(array $form, FormStateInterface $form_state, $drug_news_revision = NULL) {
    $this->revision = $this->DrugNewsStorage->loadRevision($drug_news_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->DrugNewsStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Drug news: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of Drug news %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.drug_news.canonical',
       ['drug_news' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {drug_news_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.drug_news.version_history',
         ['drug_news' => $this->revision->id()]
      );
    }
  }

}
