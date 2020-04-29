<?php

namespace Drupal\cme_link\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\cme_link\Entity\CmeLinksInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CmeLinksController.
 *
 *  Returns responses for CME Links routes.
 */
class CmeLinksController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a CME Links revision.
   *
   * @param int $cme_links_revision
   *   The CME Links revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($cme_links_revision) {
    $cme_links = $this->entityTypeManager()->getStorage('cme_links')
      ->loadRevision($cme_links_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('cme_links');

    return $view_builder->view($cme_links);
  }

  /**
   * Page title callback for a CME Links revision.
   *
   * @param int $cme_links_revision
   *   The CME Links revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($cme_links_revision) {
    $cme_links = $this->entityTypeManager()->getStorage('cme_links')
      ->loadRevision($cme_links_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $cme_links->label(),
      '%date' => $this->dateFormatter->format($cme_links->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a CME Links.
   *
   * @param \Drupal\cme_link\Entity\CmeLinksInterface $cme_links
   *   A CME Links object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(CmeLinksInterface $cme_links) {
    $account = $this->currentUser();
    $cme_links_storage = $this->entityTypeManager()->getStorage('cme_links');

    $langcode = $cme_links->language()->getId();
    $langname = $cme_links->language()->getName();
    $languages = $cme_links->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $cme_links->label()]) : $this->t('Revisions for %title', ['%title' => $cme_links->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all cme links revisions") || $account->hasPermission('administer cme links entities')));
    $delete_permission = (($account->hasPermission("delete all cme links revisions") || $account->hasPermission('administer cme links entities')));

    $rows = [];

    $vids = $cme_links_storage->revisionIds($cme_links);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\cme_link\CmeLinksInterface $revision */
      $revision = $cme_links_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $cme_links->getRevisionId()) {
          $link = $this->l($date, new Url('entity.cme_links.revision', [
            'cme_links' => $cme_links->id(),
            'cme_links_revision' => $vid,
          ]));
        }
        else {
          $link = $cme_links->link($date);
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
              Url::fromRoute('entity.cme_links.translation_revert', [
                'cme_links' => $cme_links->id(),
                'cme_links_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.cme_links.revision_revert', [
                'cme_links' => $cme_links->id(),
                'cme_links_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.cme_links.revision_delete', [
                'cme_links' => $cme_links->id(),
                'cme_links_revision' => $vid,
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

    $build['cme_links_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
