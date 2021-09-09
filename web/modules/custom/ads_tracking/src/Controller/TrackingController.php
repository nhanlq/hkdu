<?php

namespace Drupal\ads_tracking\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\ads_tracking\Entity\TrackingInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TrackingController.
 *
 *  Returns responses for Tracking routes.
 */
class TrackingController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Tracking revision.
   *
   * @param int $tracking_revision
   *   The Tracking revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($tracking_revision) {
    $tracking = $this->entityTypeManager()->getStorage('tracking')
      ->loadRevision($tracking_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('tracking');

    return $view_builder->view($tracking);
  }

  /**
   * Page title callback for a Tracking revision.
   *
   * @param int $tracking_revision
   *   The Tracking revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($tracking_revision) {
    $tracking = $this->entityTypeManager()->getStorage('tracking')
      ->loadRevision($tracking_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $tracking->label(),
      '%date' => $this->dateFormatter->format($tracking->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Tracking.
   *
   * @param \Drupal\ads_tracking\Entity\TrackingInterface $tracking
   *   A Tracking object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(TrackingInterface $tracking) {
    $account = $this->currentUser();
    $tracking_storage = $this->entityTypeManager()->getStorage('tracking');

    $langcode = $tracking->language()->getId();
    $langname = $tracking->language()->getName();
    $languages = $tracking->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $tracking->label()]) : $this->t('Revisions for %title', ['%title' => $tracking->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all tracking revisions") || $account->hasPermission('administer tracking entities')));
    $delete_permission = (($account->hasPermission("delete all tracking revisions") || $account->hasPermission('administer tracking entities')));

    $rows = [];

    $vids = $tracking_storage->revisionIds($tracking);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\ads_tracking\TrackingInterface $revision */
      $revision = $tracking_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $tracking->getRevisionId()) {
          $link = $this->l($date, new Url('entity.tracking.revision', [
            'tracking' => $tracking->id(),
            'tracking_revision' => $vid,
          ]));
        }
        else {
          $link = $tracking->link($date);
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
              Url::fromRoute('entity.tracking.translation_revert', [
                'tracking' => $tracking->id(),
                'tracking_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.tracking.revision_revert', [
                'tracking' => $tracking->id(),
                'tracking_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.tracking.revision_delete', [
                'tracking' => $tracking->id(),
                'tracking_revision' => $vid,
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

    $build['tracking_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
