<?php

namespace Drupal\drug_news\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\drug_news\Entity\DrugNewsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DrugNewsController.
 *
 *  Returns responses for Drug news routes.
 */
class DrugNewsController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Drug news revision.
   *
   * @param int $drug_news_revision
   *   The Drug news revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($drug_news_revision) {
    $drug_news = $this->entityTypeManager()->getStorage('drug_news')
      ->loadRevision($drug_news_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('drug_news');

    return $view_builder->view($drug_news);
  }

  /**
   * Page title callback for a Drug news revision.
   *
   * @param int $drug_news_revision
   *   The Drug news revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($drug_news_revision) {
    $drug_news = $this->entityTypeManager()->getStorage('drug_news')
      ->loadRevision($drug_news_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $drug_news->label(),
      '%date' => $this->dateFormatter->format($drug_news->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Drug news.
   *
   * @param \Drupal\drug_news\Entity\DrugNewsInterface $drug_news
   *   A Drug news object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(DrugNewsInterface $drug_news) {
    $account = $this->currentUser();
    $drug_news_storage = $this->entityTypeManager()->getStorage('drug_news');

    $langcode = $drug_news->language()->getId();
    $langname = $drug_news->language()->getName();
    $languages = $drug_news->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $drug_news->label()]) : $this->t('Revisions for %title', ['%title' => $drug_news->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all drug news revisions") || $account->hasPermission('administer drug news entities')));
    $delete_permission = (($account->hasPermission("delete all drug news revisions") || $account->hasPermission('administer drug news entities')));

    $rows = [];

    $vids = $drug_news_storage->revisionIds($drug_news);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\drug_news\DrugNewsInterface $revision */
      $revision = $drug_news_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $drug_news->getRevisionId()) {
          $link = $this->l($date, new Url('entity.drug_news.revision', [
            'drug_news' => $drug_news->id(),
            'drug_news_revision' => $vid,
          ]));
        }
        else {
          $link = $drug_news->link($date);
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
              Url::fromRoute('entity.drug_news.translation_revert', [
                'drug_news' => $drug_news->id(),
                'drug_news_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.drug_news.revision_revert', [
                'drug_news' => $drug_news->id(),
                'drug_news_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.drug_news.revision_delete', [
                'drug_news' => $drug_news->id(),
                'drug_news_revision' => $vid,
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

    $build['drug_news_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
