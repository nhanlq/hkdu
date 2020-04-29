<?php

namespace Drupal\gallery\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\gallery\Entity\GalleryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GalleryController.
 *
 *  Returns responses for Gallery routes.
 */
class GalleryController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Gallery revision.
   *
   * @param int $gallery_revision
   *   The Gallery revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($gallery_revision) {
    $gallery = $this->entityTypeManager()->getStorage('gallery')
      ->loadRevision($gallery_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('gallery');

    return $view_builder->view($gallery);
  }

  /**
   * Page title callback for a Gallery revision.
   *
   * @param int $gallery_revision
   *   The Gallery revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($gallery_revision) {
    $gallery = $this->entityTypeManager()->getStorage('gallery')
      ->loadRevision($gallery_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $gallery->label(),
      '%date' => $this->dateFormatter->format($gallery->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Gallery.
   *
   * @param \Drupal\gallery\Entity\GalleryInterface $gallery
   *   A Gallery object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(GalleryInterface $gallery) {
    $account = $this->currentUser();
    $gallery_storage = $this->entityTypeManager()->getStorage('gallery');

    $langcode = $gallery->language()->getId();
    $langname = $gallery->language()->getName();
    $languages = $gallery->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $gallery->label()]) : $this->t('Revisions for %title', ['%title' => $gallery->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all gallery revisions") || $account->hasPermission('administer gallery entities')));
    $delete_permission = (($account->hasPermission("delete all gallery revisions") || $account->hasPermission('administer gallery entities')));

    $rows = [];

    $vids = $gallery_storage->revisionIds($gallery);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\gallery\GalleryInterface $revision */
      $revision = $gallery_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $gallery->getRevisionId()) {
          $link = $this->l($date, new Url('entity.gallery.revision', [
            'gallery' => $gallery->id(),
            'gallery_revision' => $vid,
          ]));
        }
        else {
          $link = $gallery->link($date);
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
              Url::fromRoute('entity.gallery.translation_revert', [
                'gallery' => $gallery->id(),
                'gallery_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.gallery.revision_revert', [
                'gallery' => $gallery->id(),
                'gallery_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.gallery.revision_delete', [
                'gallery' => $gallery->id(),
                'gallery_revision' => $vid,
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

    $build['gallery_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
