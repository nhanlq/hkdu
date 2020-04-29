<?php

namespace Drupal\drug_search\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\drug_search\Entity\DrugSearchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DrugSearchController.
 *
 *  Returns responses for Drug search routes.
 */
class DrugSearchController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a Drug search revision.
   *
   * @param int $drug_search_revision
   *   The Drug search revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($drug_search_revision) {
    $drug_search = $this->entityTypeManager()->getStorage('drug_search')
      ->loadRevision($drug_search_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('drug_search');

    return $view_builder->view($drug_search);
  }

  /**
   * Page title callback for a Drug search revision.
   *
   * @param int $drug_search_revision
   *   The Drug search revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($drug_search_revision) {
    $drug_search = $this->entityTypeManager()->getStorage('drug_search')
      ->loadRevision($drug_search_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $drug_search->label(),
      '%date' => $this->dateFormatter->format($drug_search->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Drug search.
   *
   * @param \Drupal\drug_search\Entity\DrugSearchInterface $drug_search
   *   A Drug search object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(DrugSearchInterface $drug_search) {
    $account = $this->currentUser();
    $drug_search_storage = $this->entityTypeManager()->getStorage('drug_search');

    $langcode = $drug_search->language()->getId();
    $langname = $drug_search->language()->getName();
    $languages = $drug_search->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $drug_search->label()]) : $this->t('Revisions for %title', ['%title' => $drug_search->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all drug search revisions") || $account->hasPermission('administer drug search entities')));
    $delete_permission = (($account->hasPermission("delete all drug search revisions") || $account->hasPermission('administer drug search entities')));

    $rows = [];

    $vids = $drug_search_storage->revisionIds($drug_search);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\drug_search\DrugSearchInterface $revision */
      $revision = $drug_search_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $drug_search->getRevisionId()) {
          $link = $this->l($date, new Url('entity.drug_search.revision', [
            'drug_search' => $drug_search->id(),
            'drug_search_revision' => $vid,
          ]));
        }
        else {
          $link = $drug_search->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.drug_search.translation_revert', [
                'drug_search' => $drug_search->id(),
                'drug_search_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.drug_search.revision_revert', [
                'drug_search' => $drug_search->id(),
                'drug_search_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.drug_search.revision_delete', [
                'drug_search' => $drug_search->id(),
                'drug_search_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['drug_search_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
