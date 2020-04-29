<?php

namespace Drupal\special_offer\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\special_offer\Entity\SpecialOfferInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SpecialOfferController.
 *
 *  Returns responses for Special offer routes.
 */
class SpecialOfferController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Special offer revision.
   *
   * @param int $special_offer_revision
   *   The Special offer revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($special_offer_revision) {
    $special_offer = $this->entityTypeManager()->getStorage('special_offer')
      ->loadRevision($special_offer_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('special_offer');

    return $view_builder->view($special_offer);
  }

  /**
   * Page title callback for a Special offer revision.
   *
   * @param int $special_offer_revision
   *   The Special offer revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($special_offer_revision) {
    $special_offer = $this->entityTypeManager()->getStorage('special_offer')
      ->loadRevision($special_offer_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $special_offer->label(),
      '%date' => $this->dateFormatter->format($special_offer->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Special offer.
   *
   * @param \Drupal\special_offer\Entity\SpecialOfferInterface $special_offer
   *   A Special offer object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(SpecialOfferInterface $special_offer) {
    $account = $this->currentUser();
    $special_offer_storage = $this->entityTypeManager()->getStorage('special_offer');

    $langcode = $special_offer->language()->getId();
    $langname = $special_offer->language()->getName();
    $languages = $special_offer->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $special_offer->label()]) : $this->t('Revisions for %title', ['%title' => $special_offer->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all special offer revisions") || $account->hasPermission('administer special offer entities')));
    $delete_permission = (($account->hasPermission("delete all special offer revisions") || $account->hasPermission('administer special offer entities')));

    $rows = [];

    $vids = $special_offer_storage->revisionIds($special_offer);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\special_offer\SpecialOfferInterface $revision */
      $revision = $special_offer_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $special_offer->getRevisionId()) {
          $link = $this->l($date, new Url('entity.special_offer.revision', [
            'special_offer' => $special_offer->id(),
            'special_offer_revision' => $vid,
          ]));
        }
        else {
          $link = $special_offer->link($date);
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
              Url::fromRoute('entity.special_offer.translation_revert', [
                'special_offer' => $special_offer->id(),
                'special_offer_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.special_offer.revision_revert', [
                'special_offer' => $special_offer->id(),
                'special_offer_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.special_offer.revision_delete', [
                'special_offer' => $special_offer->id(),
                'special_offer_revision' => $vid,
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

    $build['special_offer_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
