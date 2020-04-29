<?php

namespace Drupal\quiz_tracking\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\quiz_tracking\Entity\QuizTrackingInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class QuizTrackingController.
 *
 *  Returns responses for Quiz tracking routes.
 */
class QuizTrackingController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Quiz tracking revision.
   *
   * @param int $quiz_tracking_revision
   *   The Quiz tracking revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($quiz_tracking_revision) {
    $quiz_tracking = $this->entityTypeManager()->getStorage('quiz_tracking')
      ->loadRevision($quiz_tracking_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('quiz_tracking');

    return $view_builder->view($quiz_tracking);
  }

  /**
   * Page title callback for a Quiz tracking revision.
   *
   * @param int $quiz_tracking_revision
   *   The Quiz tracking revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($quiz_tracking_revision) {
    $quiz_tracking = $this->entityTypeManager()->getStorage('quiz_tracking')
      ->loadRevision($quiz_tracking_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $quiz_tracking->label(),
      '%date' => $this->dateFormatter->format($quiz_tracking->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Quiz tracking.
   *
   * @param \Drupal\quiz_tracking\Entity\QuizTrackingInterface $quiz_tracking
   *   A Quiz tracking object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(QuizTrackingInterface $quiz_tracking) {
    $account = $this->currentUser();
    $quiz_tracking_storage = $this->entityTypeManager()->getStorage('quiz_tracking');

    $langcode = $quiz_tracking->language()->getId();
    $langname = $quiz_tracking->language()->getName();
    $languages = $quiz_tracking->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $quiz_tracking->label()]) : $this->t('Revisions for %title', ['%title' => $quiz_tracking->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all quiz tracking revisions") || $account->hasPermission('administer quiz tracking entities')));
    $delete_permission = (($account->hasPermission("delete all quiz tracking revisions") || $account->hasPermission('administer quiz tracking entities')));

    $rows = [];

    $vids = $quiz_tracking_storage->revisionIds($quiz_tracking);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\quiz_tracking\QuizTrackingInterface $revision */
      $revision = $quiz_tracking_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $quiz_tracking->getRevisionId()) {
          $link = $this->l($date, new Url('entity.quiz_tracking.revision', [
            'quiz_tracking' => $quiz_tracking->id(),
            'quiz_tracking_revision' => $vid,
          ]));
        }
        else {
          $link = $quiz_tracking->link($date);
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
              Url::fromRoute('entity.quiz_tracking.translation_revert', [
                'quiz_tracking' => $quiz_tracking->id(),
                'quiz_tracking_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.quiz_tracking.revision_revert', [
                'quiz_tracking' => $quiz_tracking->id(),
                'quiz_tracking_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.quiz_tracking.revision_delete', [
                'quiz_tracking' => $quiz_tracking->id(),
                'quiz_tracking_revision' => $vid,
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

    $build['quiz_tracking_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
