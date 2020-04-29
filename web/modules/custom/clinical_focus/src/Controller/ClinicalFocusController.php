<?php

namespace Drupal\clinical_focus\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\clinical_focus\Entity\ClinicalFocusInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ClinicalFocusController.
 *
 *  Returns responses for Clinical focus routes.
 */
class ClinicalFocusController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Clinical focus revision.
   *
   * @param int $clinical_focus_revision
   *   The Clinical focus revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($clinical_focus_revision) {
    $clinical_focus = $this->entityTypeManager()->getStorage('clinical_focus')
      ->loadRevision($clinical_focus_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('clinical_focus');

    return $view_builder->view($clinical_focus);
  }

  /**
   * Page title callback for a Clinical focus revision.
   *
   * @param int $clinical_focus_revision
   *   The Clinical focus revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($clinical_focus_revision) {
    $clinical_focus = $this->entityTypeManager()->getStorage('clinical_focus')
      ->loadRevision($clinical_focus_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $clinical_focus->label(),
      '%date' => $this->dateFormatter->format($clinical_focus->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Clinical focus.
   *
   * @param \Drupal\clinical_focus\Entity\ClinicalFocusInterface $clinical_focus
   *   A Clinical focus object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ClinicalFocusInterface $clinical_focus) {
    $account = $this->currentUser();
    $clinical_focus_storage = $this->entityTypeManager()->getStorage('clinical_focus');

    $langcode = $clinical_focus->language()->getId();
    $langname = $clinical_focus->language()->getName();
    $languages = $clinical_focus->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $clinical_focus->label()]) : $this->t('Revisions for %title', ['%title' => $clinical_focus->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all clinical focus revisions") || $account->hasPermission('administer clinical focus entities')));
    $delete_permission = (($account->hasPermission("delete all clinical focus revisions") || $account->hasPermission('administer clinical focus entities')));

    $rows = [];

    $vids = $clinical_focus_storage->revisionIds($clinical_focus);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\clinical_focus\ClinicalFocusInterface $revision */
      $revision = $clinical_focus_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $clinical_focus->getRevisionId()) {
          $link = $this->l($date, new Url('entity.clinical_focus.revision', [
            'clinical_focus' => $clinical_focus->id(),
            'clinical_focus_revision' => $vid,
          ]));
        }
        else {
          $link = $clinical_focus->link($date);
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
              Url::fromRoute('entity.clinical_focus.translation_revert', [
                'clinical_focus' => $clinical_focus->id(),
                'clinical_focus_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.clinical_focus.revision_revert', [
                'clinical_focus' => $clinical_focus->id(),
                'clinical_focus_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.clinical_focus.revision_delete', [
                'clinical_focus' => $clinical_focus->id(),
                'clinical_focus_revision' => $vid,
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

    $build['clinical_focus_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
