<?php

namespace Drupal\notify\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\notify\Entity\UserNotifyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class UserNotifyController.
 *
 *  Returns responses for User notify routes.
 */
class UserNotifyController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a User notify revision.
   *
   * @param int $user_notify_revision
   *   The User notify revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($user_notify_revision) {
    $user_notify = $this->entityTypeManager()->getStorage('user_notify')
      ->loadRevision($user_notify_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('user_notify');

    return $view_builder->view($user_notify);
  }

  /**
   * Page title callback for a User notify revision.
   *
   * @param int $user_notify_revision
   *   The User notify revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($user_notify_revision) {
    $user_notify = $this->entityTypeManager()->getStorage('user_notify')
      ->loadRevision($user_notify_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $user_notify->label(),
      '%date' => $this->dateFormatter->format($user_notify->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a User notify.
   *
   * @param \Drupal\notify\Entity\UserNotifyInterface $user_notify
   *   A User notify object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(UserNotifyInterface $user_notify) {
    $account = $this->currentUser();
    $user_notify_storage = $this->entityTypeManager()->getStorage('user_notify');

    $langcode = $user_notify->language()->getId();
    $langname = $user_notify->language()->getName();
    $languages = $user_notify->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $user_notify->label()]) : $this->t('Revisions for %title', ['%title' => $user_notify->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all user notify revisions") || $account->hasPermission('administer user notify entities')));
    $delete_permission = (($account->hasPermission("delete all user notify revisions") || $account->hasPermission('administer user notify entities')));

    $rows = [];

    $vids = $user_notify_storage->revisionIds($user_notify);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\notify\UserNotifyInterface $revision */
      $revision = $user_notify_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $user_notify->getRevisionId()) {
          $link = $this->l($date, new Url('entity.user_notify.revision', [
            'user_notify' => $user_notify->id(),
            'user_notify_revision' => $vid,
          ]));
        }
        else {
          $link = $user_notify->link($date);
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
              Url::fromRoute('entity.user_notify.translation_revert', [
                'user_notify' => $user_notify->id(),
                'user_notify_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.user_notify.revision_revert', [
                'user_notify' => $user_notify->id(),
                'user_notify_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.user_notify.revision_delete', [
                'user_notify' => $user_notify->id(),
                'user_notify_revision' => $vid,
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

    $build['user_notify_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
