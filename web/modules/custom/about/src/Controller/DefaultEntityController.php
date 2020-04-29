<?php

namespace Drupal\about\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\about\Entity\DefaultEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DefaultEntityController.
 *
 *  Returns responses for Default entity routes.
 */
class DefaultEntityController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Default entity revision.
   *
   * @param int $about_revision
   *   The Default entity revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($about_revision) {
    $about = $this->entityTypeManager()->getStorage('about')
      ->loadRevision($about_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('about');

    return $view_builder->view($about);
  }

  /**
   * Page title callback for a Default entity revision.
   *
   * @param int $about_revision
   *   The Default entity revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($about_revision) {
    $about = $this->entityTypeManager()->getStorage('about')
      ->loadRevision($about_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $about->label(),
      '%date' => $this->dateFormatter->format($about->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Default entity.
   *
   * @param \Drupal\about\Entity\DefaultEntityInterface $about
   *   A Default entity object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(DefaultEntityInterface $about) {
    $account = $this->currentUser();
    $about_storage = $this->entityTypeManager()->getStorage('about');

    $langcode = $about->language()->getId();
    $langname = $about->language()->getName();
    $languages = $about->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $about->label()]) : $this->t('Revisions for %title', ['%title' => $about->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all default entity revisions") || $account->hasPermission('administer default entity entities')));
    $delete_permission = (($account->hasPermission("delete all default entity revisions") || $account->hasPermission('administer default entity entities')));

    $rows = [];

    $vids = $about_storage->revisionIds($about);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\about\DefaultEntityInterface $revision */
      $revision = $about_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $about->getRevisionId()) {
          $link = $this->l($date, new Url('entity.about.revision', [
            'about' => $about->id(),
            'about_revision' => $vid,
          ]));
        }
        else {
          $link = $about->link($date);
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
              Url::fromRoute('entity.about.translation_revert', [
                'about' => $about->id(),
                'about_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.about.revision_revert', [
                'about' => $about->id(),
                'about_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.about.revision_delete', [
                'about' => $about->id(),
                'about_revision' => $vid,
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

    $build['about_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
