<?php

namespace Drupal\media_release\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\media_release\Entity\MediaEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MediaEntityController.
 *
 *  Returns responses for Media entity routes.
 */
class MediaEntityController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Media entity revision.
   *
   * @param int $media_entity_revision
   *   The Media entity revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($media_entity_revision) {
    $media_entity = $this->entityTypeManager()->getStorage('media_entity')
      ->loadRevision($media_entity_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('media_entity');

    return $view_builder->view($media_entity);
  }

  /**
   * Page title callback for a Media entity revision.
   *
   * @param int $media_entity_revision
   *   The Media entity revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($media_entity_revision) {
    $media_entity = $this->entityTypeManager()->getStorage('media_entity')
      ->loadRevision($media_entity_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $media_entity->label(),
      '%date' => $this->dateFormatter->format($media_entity->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Media entity.
   *
   * @param \Drupal\media_release\Entity\MediaEntityInterface $media_entity
   *   A Media entity object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(MediaEntityInterface $media_entity) {
    $account = $this->currentUser();
    $media_entity_storage = $this->entityTypeManager()->getStorage('media_entity');

    $langcode = $media_entity->language()->getId();
    $langname = $media_entity->language()->getName();
    $languages = $media_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $media_entity->label()]) : $this->t('Revisions for %title', ['%title' => $media_entity->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all media entity revisions") || $account->hasPermission('administer media entity entities')));
    $delete_permission = (($account->hasPermission("delete all media entity revisions") || $account->hasPermission('administer media entity entities')));

    $rows = [];

    $vids = $media_entity_storage->revisionIds($media_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\media_release\MediaEntityInterface $revision */
      $revision = $media_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $media_entity->getRevisionId()) {
          $link = $this->l($date, new Url('entity.media_entity.revision', [
            'media_entity' => $media_entity->id(),
            'media_entity_revision' => $vid,
          ]));
        }
        else {
          $link = $media_entity->link($date);
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
              Url::fromRoute('entity.media_entity.translation_revert', [
                'media_entity' => $media_entity->id(),
                'media_entity_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.media_entity.revision_revert', [
                'media_entity' => $media_entity->id(),
                'media_entity_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.media_entity.revision_delete', [
                'media_entity' => $media_entity->id(),
                'media_entity_revision' => $vid,
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

    $build['media_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
