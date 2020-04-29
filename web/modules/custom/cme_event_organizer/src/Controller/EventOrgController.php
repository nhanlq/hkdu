<?php

namespace Drupal\cme_event_organizer\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\cme_event_organizer\Entity\EventOrgInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EventOrgController.
 *
 *  Returns responses for Event Organizer routes.
 */
class EventOrgController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Event Organizer revision.
   *
   * @param int $event_org_revision
   *   The Event Organizer revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($event_org_revision) {
    $event_org = $this->entityTypeManager()->getStorage('event_org')
      ->loadRevision($event_org_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('event_org');

    return $view_builder->view($event_org);
  }

  /**
   * Page title callback for a Event Organizer revision.
   *
   * @param int $event_org_revision
   *   The Event Organizer revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($event_org_revision) {
    $event_org = $this->entityTypeManager()->getStorage('event_org')
      ->loadRevision($event_org_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $event_org->label(),
      '%date' => $this->dateFormatter->format($event_org->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Event Organizer.
   *
   * @param \Drupal\cme_event_organizer\Entity\EventOrgInterface $event_org
   *   A Event Organizer object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(EventOrgInterface $event_org) {
    $account = $this->currentUser();
    $event_org_storage = $this->entityTypeManager()->getStorage('event_org');

    $langcode = $event_org->language()->getId();
    $langname = $event_org->language()->getName();
    $languages = $event_org->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $event_org->label()]) : $this->t('Revisions for %title', ['%title' => $event_org->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all event organizer revisions") || $account->hasPermission('administer event organizer entities')));
    $delete_permission = (($account->hasPermission("delete all event organizer revisions") || $account->hasPermission('administer event organizer entities')));

    $rows = [];

    $vids = $event_org_storage->revisionIds($event_org);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\cme_event_organizer\EventOrgInterface $revision */
      $revision = $event_org_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $event_org->getRevisionId()) {
          $link = $this->l($date, new Url('entity.event_org.revision', [
            'event_org' => $event_org->id(),
            'event_org_revision' => $vid,
          ]));
        }
        else {
          $link = $event_org->link($date);
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
              Url::fromRoute('entity.event_org.translation_revert', [
                'event_org' => $event_org->id(),
                'event_org_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.event_org.revision_revert', [
                'event_org' => $event_org->id(),
                'event_org_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.event_org.revision_delete', [
                'event_org' => $event_org->id(),
                'event_org_revision' => $vid,
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

    $build['event_org_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
