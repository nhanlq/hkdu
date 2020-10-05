<?php

namespace Drupal\advert\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\advert\Entity\AdsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AdsController.
 *
 *  Returns responses for Ads routes.
 */
class AdsController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Ads revision.
   *
   * @param int $ads_revision
   *   The Ads revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($ads_revision) {
    $ads = $this->entityTypeManager()->getStorage('ads')
      ->loadRevision($ads_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('ads');

    return $view_builder->view($ads);
  }

  /**
   * Page title callback for a Ads revision.
   *
   * @param int $ads_revision
   *   The Ads revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($ads_revision) {
    $ads = $this->entityTypeManager()->getStorage('ads')
      ->loadRevision($ads_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $ads->label(),
      '%date' => $this->dateFormatter->format($ads->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Ads.
   *
   * @param \Drupal\advert\Entity\AdsInterface $ads
   *   A Ads object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(AdsInterface $ads) {
    $account = $this->currentUser();
    $ads_storage = $this->entityTypeManager()->getStorage('ads');

    $langcode = $ads->language()->getId();
    $langname = $ads->language()->getName();
    $languages = $ads->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $ads->label()]) : $this->t('Revisions for %title', ['%title' => $ads->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all ads revisions") || $account->hasPermission('administer ads entities')));
    $delete_permission = (($account->hasPermission("delete all ads revisions") || $account->hasPermission('administer ads entities')));

    $rows = [];

    $vids = $ads_storage->revisionIds($ads);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\advert\AdsInterface $revision */
      $revision = $ads_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $ads->getRevisionId()) {
          $link = $this->l($date, new Url('entity.ads.revision', [
            'ads' => $ads->id(),
            'ads_revision' => $vid,
          ]));
        }
        else {
          $link = $ads->link($date);
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
              Url::fromRoute('entity.ads.translation_revert', [
                'ads' => $ads->id(),
                'ads_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.ads.revision_revert', [
                'ads' => $ads->id(),
                'ads_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.ads.revision_delete', [
                'ads' => $ads->id(),
                'ads_revision' => $vid,
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

    $build['ads_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
