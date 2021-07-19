<?php

namespace Drupal\payment_upload\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\payment_upload\Entity\PaymentUploadInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PaymentUploadController.
 *
 *  Returns responses for Payment upload routes.
 */
class PaymentUploadController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Payment upload revision.
   *
   * @param int $payment_upload_revision
   *   The Payment upload revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($payment_upload_revision) {
    $payment_upload = $this->entityTypeManager()->getStorage('payment_upload')
      ->loadRevision($payment_upload_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('payment_upload');

    return $view_builder->view($payment_upload);
  }

  /**
   * Page title callback for a Payment upload revision.
   *
   * @param int $payment_upload_revision
   *   The Payment upload revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($payment_upload_revision) {
    $payment_upload = $this->entityTypeManager()->getStorage('payment_upload')
      ->loadRevision($payment_upload_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $payment_upload->label(),
      '%date' => $this->dateFormatter->format($payment_upload->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Payment upload.
   *
   * @param \Drupal\payment_upload\Entity\PaymentUploadInterface $payment_upload
   *   A Payment upload object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(PaymentUploadInterface $payment_upload) {
    $account = $this->currentUser();
    $payment_upload_storage = $this->entityTypeManager()->getStorage('payment_upload');

    $langcode = $payment_upload->language()->getId();
    $langname = $payment_upload->language()->getName();
    $languages = $payment_upload->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $payment_upload->label()]) : $this->t('Revisions for %title', ['%title' => $payment_upload->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all payment upload revisions") || $account->hasPermission('administer payment upload entities')));
    $delete_permission = (($account->hasPermission("delete all payment upload revisions") || $account->hasPermission('administer payment upload entities')));

    $rows = [];

    $vids = $payment_upload_storage->revisionIds($payment_upload);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\payment_upload\PaymentUploadInterface $revision */
      $revision = $payment_upload_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $payment_upload->getRevisionId()) {
          $link = $this->l($date, new Url('entity.payment_upload.revision', [
            'payment_upload' => $payment_upload->id(),
            'payment_upload_revision' => $vid,
          ]));
        }
        else {
          $link = $payment_upload->link($date);
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
              Url::fromRoute('entity.payment_upload.translation_revert', [
                'payment_upload' => $payment_upload->id(),
                'payment_upload_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.payment_upload.revision_revert', [
                'payment_upload' => $payment_upload->id(),
                'payment_upload_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.payment_upload.revision_delete', [
                'payment_upload' => $payment_upload->id(),
                'payment_upload_revision' => $vid,
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

    $build['payment_upload_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
