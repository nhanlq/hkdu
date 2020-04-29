<?php

namespace Drupal\doctor\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\doctor\Entity\DoctorEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DoctorEntityController.
 *
 *  Returns responses for Doctor entity routes.
 */
class DoctorEntityController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Doctor entity revision.
   *
   * @param int $doctor_revision
   *   The Doctor entity revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($doctor_revision) {
    $doctor = $this->entityTypeManager()->getStorage('doctor')
      ->loadRevision($doctor_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('doctor');

    return $view_builder->view($doctor);
  }

  /**
   * Page title callback for a Doctor entity revision.
   *
   * @param int $doctor_revision
   *   The Doctor entity revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($doctor_revision) {
    $doctor = $this->entityTypeManager()->getStorage('doctor')
      ->loadRevision($doctor_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $doctor->label(),
      '%date' => $this->dateFormatter->format($doctor->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Doctor entity.
   *
   * @param \Drupal\doctor\Entity\DoctorEntityInterface $doctor
   *   A Doctor entity object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(DoctorEntityInterface $doctor) {
    $account = $this->currentUser();
    $doctor_storage = $this->entityTypeManager()->getStorage('doctor');

    $langcode = $doctor->language()->getId();
    $langname = $doctor->language()->getName();
    $languages = $doctor->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $doctor->label()]) : $this->t('Revisions for %title', ['%title' => $doctor->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all doctor entity revisions") || $account->hasPermission('administer doctor entity entities')));
    $delete_permission = (($account->hasPermission("delete all doctor entity revisions") || $account->hasPermission('administer doctor entity entities')));

    $rows = [];

    $vids = $doctor_storage->revisionIds($doctor);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\doctor\DoctorEntityInterface $revision */
      $revision = $doctor_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $doctor->getRevisionId()) {
          $link = $this->l($date, new Url('entity.doctor.revision', [
            'doctor' => $doctor->id(),
            'doctor_revision' => $vid,
          ]));
        }
        else {
          $link = $doctor->link($date);
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
              Url::fromRoute('entity.doctor.translation_revert', [
                'doctor' => $doctor->id(),
                'doctor_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.doctor.revision_revert', [
                'doctor' => $doctor->id(),
                'doctor_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.doctor.revision_delete', [
                'doctor' => $doctor->id(),
                'doctor_revision' => $vid,
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

    $build['doctor_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
