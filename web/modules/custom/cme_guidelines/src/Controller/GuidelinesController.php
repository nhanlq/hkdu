<?php

namespace Drupal\cme_guidelines\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\cme_guidelines\Entity\GuidelinesInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GuidelinesController.
 *
 *  Returns responses for Guidelines routes.
 */
class GuidelinesController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Guidelines revision.
   *
   * @param int $guidelines_revision
   *   The Guidelines revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($guidelines_revision) {
    $guidelines = $this->entityTypeManager()->getStorage('guidelines')
      ->loadRevision($guidelines_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('guidelines');

    return $view_builder->view($guidelines);
  }

  /**
   * Page title callback for a Guidelines revision.
   *
   * @param int $guidelines_revision
   *   The Guidelines revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($guidelines_revision) {
    $guidelines = $this->entityTypeManager()->getStorage('guidelines')
      ->loadRevision($guidelines_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $guidelines->label(),
      '%date' => $this->dateFormatter->format($guidelines->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Guidelines.
   *
   * @param \Drupal\cme_guidelines\Entity\GuidelinesInterface $guidelines
   *   A Guidelines object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(GuidelinesInterface $guidelines) {
    $account = $this->currentUser();
    $guidelines_storage = $this->entityTypeManager()->getStorage('guidelines');

    $langcode = $guidelines->language()->getId();
    $langname = $guidelines->language()->getName();
    $languages = $guidelines->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $guidelines->label()]) : $this->t('Revisions for %title', ['%title' => $guidelines->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all guidelines revisions") || $account->hasPermission('administer guidelines entities')));
    $delete_permission = (($account->hasPermission("delete all guidelines revisions") || $account->hasPermission('administer guidelines entities')));

    $rows = [];

    $vids = $guidelines_storage->revisionIds($guidelines);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\cme_guidelines\GuidelinesInterface $revision */
      $revision = $guidelines_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $guidelines->getRevisionId()) {
          $link = $this->l($date, new Url('entity.guidelines.revision', [
            'guidelines' => $guidelines->id(),
            'guidelines_revision' => $vid,
          ]));
        }
        else {
          $link = $guidelines->link($date);
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
              Url::fromRoute('entity.guidelines.translation_revert', [
                'guidelines' => $guidelines->id(),
                'guidelines_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.guidelines.revision_revert', [
                'guidelines' => $guidelines->id(),
                'guidelines_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.guidelines.revision_delete', [
                'guidelines' => $guidelines->id(),
                'guidelines_revision' => $vid,
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

    $build['guidelines_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
