<?php

namespace Drupal\hospital\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\hospital\Entity\HospitalEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class HospitalEntityController.
 *
 *  Returns responses for Hospital entity routes.
 */
class HospitalEntityController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Hospital entity revision.
   *
   * @param int $hospital_revision
   *   The Hospital entity revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($hospital_revision) {
    $hospital = $this->entityTypeManager()->getStorage('hospital')
      ->loadRevision($hospital_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('hospital');

    return $view_builder->view($hospital);
  }

  /**
   * Page title callback for a Hospital entity revision.
   *
   * @param int $hospital_revision
   *   The Hospital entity revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($hospital_revision) {
    $hospital = $this->entityTypeManager()->getStorage('hospital')
      ->loadRevision($hospital_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $hospital->label(),
      '%date' => $this->dateFormatter->format($hospital->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Hospital entity.
   *
   * @param \Drupal\hospital\Entity\HospitalEntityInterface $hospital
   *   A Hospital entity object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(HospitalEntityInterface $hospital) {
    $account = $this->currentUser();
    $hospital_storage = $this->entityTypeManager()->getStorage('hospital');

    $langcode = $hospital->language()->getId();
    $langname = $hospital->language()->getName();
    $languages = $hospital->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $hospital->label()]) : $this->t('Revisions for %title', ['%title' => $hospital->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all hospital entity revisions") || $account->hasPermission('administer hospital entity entities')));
    $delete_permission = (($account->hasPermission("delete all hospital entity revisions") || $account->hasPermission('administer hospital entity entities')));

    $rows = [];

    $vids = $hospital_storage->revisionIds($hospital);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\hospital\HospitalEntityInterface $revision */
      $revision = $hospital_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $hospital->getRevisionId()) {
          $link = $this->l($date, new Url('entity.hospital.revision', [
            'hospital' => $hospital->id(),
            'hospital_revision' => $vid,
          ]));
        }
        else {
          $link = $hospital->link($date);
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
              Url::fromRoute('entity.hospital.translation_revert', [
                'hospital' => $hospital->id(),
                'hospital_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.hospital.revision_revert', [
                'hospital' => $hospital->id(),
                'hospital_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.hospital.revision_delete', [
                'hospital' => $hospital->id(),
                'hospital_revision' => $vid,
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

    $build['hospital_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
