<?php

namespace Drupal\tools\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\tools\Entity\ToolsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ToolsController.
 *
 *  Returns responses for Tools routes.
 */
class ToolsController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Tools revision.
   *
   * @param int $tools_revision
   *   The Tools revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($tools_revision) {
    $tools = $this->entityTypeManager()->getStorage('tools')
      ->loadRevision($tools_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('tools');

    return $view_builder->view($tools);
  }

  /**
   * Page title callback for a Tools revision.
   *
   * @param int $tools_revision
   *   The Tools revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($tools_revision) {
    $tools = $this->entityTypeManager()->getStorage('tools')
      ->loadRevision($tools_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $tools->label(),
      '%date' => $this->dateFormatter->format($tools->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Tools.
   *
   * @param \Drupal\tools\Entity\ToolsInterface $tools
   *   A Tools object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ToolsInterface $tools) {
    $account = $this->currentUser();
    $tools_storage = $this->entityTypeManager()->getStorage('tools');

    $langcode = $tools->language()->getId();
    $langname = $tools->language()->getName();
    $languages = $tools->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $tools->label()]) : $this->t('Revisions for %title', ['%title' => $tools->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all tools revisions") || $account->hasPermission('administer tools entities')));
    $delete_permission = (($account->hasPermission("delete all tools revisions") || $account->hasPermission('administer tools entities')));

    $rows = [];

    $vids = $tools_storage->revisionIds($tools);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\tools\ToolsInterface $revision */
      $revision = $tools_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $tools->getRevisionId()) {
          $link = $this->l($date, new Url('entity.tools.revision', [
            'tools' => $tools->id(),
            'tools_revision' => $vid,
          ]));
        }
        else {
          $link = $tools->link($date);
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
              Url::fromRoute('entity.tools.translation_revert', [
                'tools' => $tools->id(),
                'tools_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.tools.revision_revert', [
                'tools' => $tools->id(),
                'tools_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.tools.revision_delete', [
                'tools' => $tools->id(),
                'tools_revision' => $vid,
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

    $build['tools_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
