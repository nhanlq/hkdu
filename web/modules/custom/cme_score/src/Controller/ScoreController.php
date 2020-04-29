<?php

namespace Drupal\cme_score\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\cme_score\Entity\ScoreInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ScoreController.
 *
 *  Returns responses for Score routes.
 */
class ScoreController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Score revision.
   *
   * @param int $score_revision
   *   The Score revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($score_revision) {
    $score = $this->entityTypeManager()->getStorage('score')
      ->loadRevision($score_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('score');

    return $view_builder->view($score);
  }

  /**
   * Page title callback for a Score revision.
   *
   * @param int $score_revision
   *   The Score revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($score_revision) {
    $score = $this->entityTypeManager()->getStorage('score')
      ->loadRevision($score_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $score->label(),
      '%date' => $this->dateFormatter->format($score->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Score.
   *
   * @param \Drupal\cme_score\Entity\ScoreInterface $score
   *   A Score object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ScoreInterface $score) {
    $account = $this->currentUser();
    $score_storage = $this->entityTypeManager()->getStorage('score');

    $langcode = $score->language()->getId();
    $langname = $score->language()->getName();
    $languages = $score->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $score->label()]) : $this->t('Revisions for %title', ['%title' => $score->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all score revisions") || $account->hasPermission('administer score entities')));
    $delete_permission = (($account->hasPermission("delete all score revisions") || $account->hasPermission('administer score entities')));

    $rows = [];

    $vids = $score_storage->revisionIds($score);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\cme_score\ScoreInterface $revision */
      $revision = $score_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $score->getRevisionId()) {
          $link = $this->l($date, new Url('entity.score.revision', [
            'score' => $score->id(),
            'score_revision' => $vid,
          ]));
        }
        else {
          $link = $score->link($date);
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
              Url::fromRoute('entity.score.translation_revert', [
                'score' => $score->id(),
                'score_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.score.revision_revert', [
                'score' => $score->id(),
                'score_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.score.revision_delete', [
                'score' => $score->id(),
                'score_revision' => $vid,
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

    $build['score_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
