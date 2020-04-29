<?php

namespace Drupal\public_links\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\public_links\Entity\PublicLinksInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PublicLinksController.
 *
 *  Returns responses for Public links routes.
 */
class PublicLinksController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Public links revision.
   *
   * @param int $public_links_revision
   *   The Public links revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($public_links_revision) {
    $public_links = $this->entityTypeManager()->getStorage('public_links')
      ->loadRevision($public_links_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('public_links');

    return $view_builder->view($public_links);
  }

  /**
   * Page title callback for a Public links revision.
   *
   * @param int $public_links_revision
   *   The Public links revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($public_links_revision) {
    $public_links = $this->entityTypeManager()->getStorage('public_links')
      ->loadRevision($public_links_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $public_links->label(),
      '%date' => $this->dateFormatter->format($public_links->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Public links.
   *
   * @param \Drupal\public_links\Entity\PublicLinksInterface $public_links
   *   A Public links object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(PublicLinksInterface $public_links) {
    $account = $this->currentUser();
    $public_links_storage = $this->entityTypeManager()->getStorage('public_links');

    $langcode = $public_links->language()->getId();
    $langname = $public_links->language()->getName();
    $languages = $public_links->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $public_links->label()]) : $this->t('Revisions for %title', ['%title' => $public_links->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all public links revisions") || $account->hasPermission('administer public links entities')));
    $delete_permission = (($account->hasPermission("delete all public links revisions") || $account->hasPermission('administer public links entities')));

    $rows = [];

    $vids = $public_links_storage->revisionIds($public_links);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\public_links\PublicLinksInterface $revision */
      $revision = $public_links_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $public_links->getRevisionId()) {
          $link = $this->l($date, new Url('entity.public_links.revision', [
            'public_links' => $public_links->id(),
            'public_links_revision' => $vid,
          ]));
        }
        else {
          $link = $public_links->link($date);
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
              Url::fromRoute('entity.public_links.translation_revert', [
                'public_links' => $public_links->id(),
                'public_links_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.public_links.revision_revert', [
                'public_links' => $public_links->id(),
                'public_links_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.public_links.revision_delete', [
                'public_links' => $public_links->id(),
                'public_links_revision' => $vid,
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

    $build['public_links_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
