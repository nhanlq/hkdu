<?php

namespace Drupal\download\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\download\Entity\DownloadInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DownloadController.
 *
 *  Returns responses for Download routes.
 */
class DownloadController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Download revision.
   *
   * @param int $download_revision
   *   The Download revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($download_revision) {
    $download = $this->entityTypeManager()->getStorage('download')
      ->loadRevision($download_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('download');

    return $view_builder->view($download);
  }

  /**
   * Page title callback for a Download revision.
   *
   * @param int $download_revision
   *   The Download revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($download_revision) {
    $download = $this->entityTypeManager()->getStorage('download')
      ->loadRevision($download_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $download->label(),
      '%date' => $this->dateFormatter->format($download->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Download.
   *
   * @param \Drupal\download\Entity\DownloadInterface $download
   *   A Download object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(DownloadInterface $download) {
    $account = $this->currentUser();
    $download_storage = $this->entityTypeManager()->getStorage('download');

    $langcode = $download->language()->getId();
    $langname = $download->language()->getName();
    $languages = $download->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $download->label()]) : $this->t('Revisions for %title', ['%title' => $download->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all download revisions") || $account->hasPermission('administer download entities')));
    $delete_permission = (($account->hasPermission("delete all download revisions") || $account->hasPermission('administer download entities')));

    $rows = [];

    $vids = $download_storage->revisionIds($download);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\download\DownloadInterface $revision */
      $revision = $download_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $download->getRevisionId()) {
          $link = $this->l($date, new Url('entity.download.revision', [
            'download' => $download->id(),
            'download_revision' => $vid,
          ]));
        }
        else {
          $link = $download->link($date);
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
              Url::fromRoute('entity.download.translation_revert', [
                'download' => $download->id(),
                'download_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.download.revision_revert', [
                'download' => $download->id(),
                'download_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.download.revision_delete', [
                'download' => $download->id(),
                'download_revision' => $vid,
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

    $build['download_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
