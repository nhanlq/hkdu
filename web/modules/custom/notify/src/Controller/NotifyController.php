<?php

namespace Drupal\notify\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\notify\Entity\NotifyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NotifyController.
 *
 *  Returns responses for Notify routes.
 */
class NotifyController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Notify revision.
   *
   * @param int $notify_revision
   *   The Notify revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($notify_revision) {
    $notify = $this->entityTypeManager()->getStorage('notify')
      ->loadRevision($notify_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('notify');

    return $view_builder->view($notify);
  }

  /**
   * Page title callback for a Notify revision.
   *
   * @param int $notify_revision
   *   The Notify revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($notify_revision) {
    $notify = $this->entityTypeManager()->getStorage('notify')
      ->loadRevision($notify_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $notify->label(),
      '%date' => $this->dateFormatter->format($notify->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Notify.
   *
   * @param \Drupal\notify\Entity\NotifyInterface $notify
   *   A Notify object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(NotifyInterface $notify) {
    $account = $this->currentUser();
    $notify_storage = $this->entityTypeManager()->getStorage('notify');

    $langcode = $notify->language()->getId();
    $langname = $notify->language()->getName();
    $languages = $notify->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $notify->label()]) : $this->t('Revisions for %title', ['%title' => $notify->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all notify revisions") || $account->hasPermission('administer notify entities')));
    $delete_permission = (($account->hasPermission("delete all notify revisions") || $account->hasPermission('administer notify entities')));

    $rows = [];

    $vids = $notify_storage->revisionIds($notify);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\notify\NotifyInterface $revision */
      $revision = $notify_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $notify->getRevisionId()) {
          $link = $this->l($date, new Url('entity.notify.revision', [
            'notify' => $notify->id(),
            'notify_revision' => $vid,
          ]));
        }
        else {
          $link = $notify->link($date);
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
              Url::fromRoute('entity.notify.translation_revert', [
                'notify' => $notify->id(),
                'notify_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.notify.revision_revert', [
                'notify' => $notify->id(),
                'notify_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.notify.revision_delete', [
                'notify' => $notify->id(),
                'notify_revision' => $vid,
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

    $build['notify_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
