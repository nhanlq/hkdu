<?php

namespace Drupal\news\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\news\Entity\NewsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NewsController.
 *
 *  Returns responses for News routes.
 */
class NewsController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a News revision.
   *
   * @param int $news_revision
   *   The News revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($news_revision) {
    $news = $this->entityTypeManager()->getStorage('news')
      ->loadRevision($news_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('news');

    return $view_builder->view($news);
  }

  /**
   * Page title callback for a News revision.
   *
   * @param int $news_revision
   *   The News revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($news_revision) {
    $news = $this->entityTypeManager()->getStorage('news')
      ->loadRevision($news_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $news->label(),
      '%date' => $this->dateFormatter->format($news->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a News.
   *
   * @param \Drupal\news\Entity\NewsInterface $news
   *   A News object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(NewsInterface $news) {
    $account = $this->currentUser();
    $news_storage = $this->entityTypeManager()->getStorage('news');

    $langcode = $news->language()->getId();
    $langname = $news->language()->getName();
    $languages = $news->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $news->label()]) : $this->t('Revisions for %title', ['%title' => $news->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all news revisions") || $account->hasPermission('administer news entities')));
    $delete_permission = (($account->hasPermission("delete all news revisions") || $account->hasPermission('administer news entities')));

    $rows = [];

    $vids = $news_storage->revisionIds($news);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\news\NewsInterface $revision */
      $revision = $news_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $news->getRevisionId()) {
          $link = $this->l($date, new Url('entity.news.revision', [
            'news' => $news->id(),
            'news_revision' => $vid,
          ]));
        }
        else {
          $link = $news->link($date);
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
              Url::fromRoute('entity.news.translation_revert', [
                'news' => $news->id(),
                'news_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.news.revision_revert', [
                'news' => $news->id(),
                'news_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.news.revision_delete', [
                'news' => $news->id(),
                'news_revision' => $vid,
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

    $build['news_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
