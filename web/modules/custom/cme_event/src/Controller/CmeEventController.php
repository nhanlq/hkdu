<?php

namespace Drupal\cme_event\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\cme_event\Entity\CmeEventInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CmeEventController.
 *
 *  Returns responses for CME Event routes.
 */
class CmeEventController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a CME Event revision.
   *
   * @param int $cme_event_revision
   *   The CME Event revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($cme_event_revision) {
    $cme_event = $this->entityTypeManager()->getStorage('cme_event')
      ->loadRevision($cme_event_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('cme_event');

    return $view_builder->view($cme_event);
  }

  /**
   * Page title callback for a CME Event revision.
   *
   * @param int $cme_event_revision
   *   The CME Event revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($cme_event_revision) {
    $cme_event = $this->entityTypeManager()->getStorage('cme_event')
      ->loadRevision($cme_event_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $cme_event->label(),
      '%date' => $this->dateFormatter->format($cme_event->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a CME Event.
   *
   * @param \Drupal\cme_event\Entity\CmeEventInterface $cme_event
   *   A CME Event object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(CmeEventInterface $cme_event) {
    $account = $this->currentUser();
    $cme_event_storage = $this->entityTypeManager()->getStorage('cme_event');

    $langcode = $cme_event->language()->getId();
    $langname = $cme_event->language()->getName();
    $languages = $cme_event->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $cme_event->label()]) : $this->t('Revisions for %title', ['%title' => $cme_event->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all cme event revisions") || $account->hasPermission('administer cme event entities')));
    $delete_permission = (($account->hasPermission("delete all cme event revisions") || $account->hasPermission('administer cme event entities')));

    $rows = [];

    $vids = $cme_event_storage->revisionIds($cme_event);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\cme_event\CmeEventInterface $revision */
      $revision = $cme_event_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $cme_event->getRevisionId()) {
          $link = $this->l($date, new Url('entity.cme_event.revision', [
            'cme_event' => $cme_event->id(),
            'cme_event_revision' => $vid,
          ]));
        }
        else {
          $link = $cme_event->link($date);
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
              Url::fromRoute('entity.cme_event.translation_revert', [
                'cme_event' => $cme_event->id(),
                'cme_event_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.cme_event.revision_revert', [
                'cme_event' => $cme_event->id(),
                'cme_event_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.cme_event.revision_delete', [
                'cme_event' => $cme_event->id(),
                'cme_event_revision' => $vid,
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

    $build['cme_event_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
