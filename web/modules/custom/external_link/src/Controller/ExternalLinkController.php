<?php

namespace Drupal\external_link\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\external_link\Entity\ExternalLinkInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ExternalLinkController.
 *
 *  Returns responses for External link routes.
 */
class ExternalLinkController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a External link revision.
   *
   * @param int $external_link_revision
   *   The External link revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($external_link_revision) {
    $external_link = $this->entityTypeManager()->getStorage('external_link')
      ->loadRevision($external_link_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('external_link');

    return $view_builder->view($external_link);
  }

  /**
   * Page title callback for a External link revision.
   *
   * @param int $external_link_revision
   *   The External link revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($external_link_revision) {
    $external_link = $this->entityTypeManager()->getStorage('external_link')
      ->loadRevision($external_link_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $external_link->label(),
      '%date' => $this->dateFormatter->format($external_link->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a External link.
   *
   * @param \Drupal\external_link\Entity\ExternalLinkInterface $external_link
   *   A External link object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ExternalLinkInterface $external_link) {
    $account = $this->currentUser();
    $external_link_storage = $this->entityTypeManager()->getStorage('external_link');

    $langcode = $external_link->language()->getId();
    $langname = $external_link->language()->getName();
    $languages = $external_link->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $external_link->label()]) : $this->t('Revisions for %title', ['%title' => $external_link->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all external link revisions") || $account->hasPermission('administer external link entities')));
    $delete_permission = (($account->hasPermission("delete all external link revisions") || $account->hasPermission('administer external link entities')));

    $rows = [];

    $vids = $external_link_storage->revisionIds($external_link);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\external_link\ExternalLinkInterface $revision */
      $revision = $external_link_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $external_link->getRevisionId()) {
          $link = $this->l($date, new Url('entity.external_link.revision', [
            'external_link' => $external_link->id(),
            'external_link_revision' => $vid,
          ]));
        }
        else {
          $link = $external_link->link($date);
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
              Url::fromRoute('entity.external_link.translation_revert', [
                'external_link' => $external_link->id(),
                'external_link_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.external_link.revision_revert', [
                'external_link' => $external_link->id(),
                'external_link_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.external_link.revision_delete', [
                'external_link' => $external_link->id(),
                'external_link_revision' => $vid,
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

    $build['external_link_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
