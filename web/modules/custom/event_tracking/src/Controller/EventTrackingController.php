<?php

namespace Drupal\event_tracking\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\event_tracking\Entity\EventTrackingInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EventTrackingController.
 *
 *  Returns responses for Event tracking routes.
 */
class EventTrackingController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Event tracking revision.
   *
   * @param int $event_tracking_revision
   *   The Event tracking revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($event_tracking_revision) {
    $event_tracking = $this->entityTypeManager()->getStorage('event_tracking')
      ->loadRevision($event_tracking_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('event_tracking');

    return $view_builder->view($event_tracking);
  }

  /**
   * Page title callback for a Event tracking revision.
   *
   * @param int $event_tracking_revision
   *   The Event tracking revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($event_tracking_revision) {
    $event_tracking = $this->entityTypeManager()->getStorage('event_tracking')
      ->loadRevision($event_tracking_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $event_tracking->label(),
      '%date' => $this->dateFormatter->format($event_tracking->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Event tracking.
   *
   * @param \Drupal\event_tracking\Entity\EventTrackingInterface $event_tracking
   *   A Event tracking object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(EventTrackingInterface $event_tracking) {
    $account = $this->currentUser();
    $event_tracking_storage = $this->entityTypeManager()->getStorage('event_tracking');

    $langcode = $event_tracking->language()->getId();
    $langname = $event_tracking->language()->getName();
    $languages = $event_tracking->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $event_tracking->label()]) : $this->t('Revisions for %title', ['%title' => $event_tracking->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all event tracking revisions") || $account->hasPermission('administer event tracking entities')));
    $delete_permission = (($account->hasPermission("delete all event tracking revisions") || $account->hasPermission('administer event tracking entities')));

    $rows = [];

    $vids = $event_tracking_storage->revisionIds($event_tracking);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\event_tracking\EventTrackingInterface $revision */
      $revision = $event_tracking_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $event_tracking->getRevisionId()) {
          $link = $this->l($date, new Url('entity.event_tracking.revision', [
            'event_tracking' => $event_tracking->id(),
            'event_tracking_revision' => $vid,
          ]));
        }
        else {
          $link = $event_tracking->link($date);
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
              Url::fromRoute('entity.event_tracking.translation_revert', [
                'event_tracking' => $event_tracking->id(),
                'event_tracking_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.event_tracking.revision_revert', [
                'event_tracking' => $event_tracking->id(),
                'event_tracking_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.event_tracking.revision_delete', [
                'event_tracking' => $event_tracking->id(),
                'event_tracking_revision' => $vid,
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

    $build['event_tracking_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
