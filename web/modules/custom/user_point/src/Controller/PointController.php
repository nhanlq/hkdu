<?php

namespace Drupal\user_point\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\user_point\Entity\PointInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PointController.
 *
 *  Returns responses for Point routes.
 */
class PointController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Point revision.
   *
   * @param int $point_revision
   *   The Point revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($point_revision) {
    $point = $this->entityTypeManager()->getStorage('point')
      ->loadRevision($point_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('point');

    return $view_builder->view($point);
  }

  /**
   * Page title callback for a Point revision.
   *
   * @param int $point_revision
   *   The Point revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($point_revision) {
    $point = $this->entityTypeManager()->getStorage('point')
      ->loadRevision($point_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $point->label(),
      '%date' => $this->dateFormatter->format($point->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Point.
   *
   * @param \Drupal\user_point\Entity\PointInterface $point
   *   A Point object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(PointInterface $point) {
    $account = $this->currentUser();
    $point_storage = $this->entityTypeManager()->getStorage('point');

    $langcode = $point->language()->getId();
    $langname = $point->language()->getName();
    $languages = $point->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $point->label()]) : $this->t('Revisions for %title', ['%title' => $point->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all point revisions") || $account->hasPermission('administer point entities')));
    $delete_permission = (($account->hasPermission("delete all point revisions") || $account->hasPermission('administer point entities')));

    $rows = [];

    $vids = $point_storage->revisionIds($point);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\user_point\PointInterface $revision */
      $revision = $point_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $point->getRevisionId()) {
          $link = $this->l($date, new Url('entity.point.revision', [
            'point' => $point->id(),
            'point_revision' => $vid,
          ]));
        }
        else {
          $link = $point->link($date);
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
              Url::fromRoute('entity.point.translation_revert', [
                'point' => $point->id(),
                'point_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.point.revision_revert', [
                'point' => $point->id(),
                'point_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.point.revision_delete', [
                'point' => $point->id(),
                'point_revision' => $vid,
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

    $build['point_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
