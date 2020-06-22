<?php

namespace Drupal\healthy\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\healthy\Entity\Healthy;
use Drupal\Component\Utility\Xss;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\healthy\Entity\HealthyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class HealthyController.
 */
class HealthyController extends ControllerBase implements ContainerInjectionInterface
{

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
    public static function create(ContainerInterface $container)
    {
        $instance = parent::create($container);
        $instance->dateFormatter = $container->get('date.formatter');
        $instance->renderer = $container->get('renderer');
        return $instance;
    }

    /**
     * Displays a News entity revision.
     *
     * @param int $news_revision
     *   The News entity revision ID.
     *
     * @return array
     *   An array suitable for drupal_render().
     */
    public function revisionShow($news_revision)
    {
        $news = $this->entityTypeManager()->getStorage('healthy')
            ->loadRevision($news_revision);
        $view_builder = $this->entityTypeManager()->getViewBuilder('healthy');

        return $view_builder->view($news);
    }

    /**
     * Page title callback for a News entity revision.
     *
     * @param int $news_revision
     *   The News entity revision ID.
     *
     * @return string
     *   The page title.
     */
    public function revisionPageTitle($news_revision)
    {
        $news = $this->entityTypeManager()->getStorage('healthy')
            ->loadRevision($news_revision);
        return $this->t('Revision of %title from %date', [
            '%title' => $news->label(),
            '%date' => $this->dateFormatter->format($news->getRevisionCreationTime()),
        ]);
    }

    /**
     * Generates an overview table of older revisions of a News entity.
     *
     * @param \Drupal\drug_news\Entity\NewsEntityInterface $news
     *   A News entity object.
     *
     * @return array
     *   An array as expected by drupal_render().
     */
    public function revisionOverview(HealthyInterface $news)
    {
        $account = $this->currentUser();
        $news_storage = $this->entityTypeManager()->getStorage('healthy');

        $langcode = $news->language()->getId();
        $langname = $news->language()->getName();
        $languages = $news->getTranslationLanguages();
        $has_translations = (count($languages) > 1);
        $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $news->label()]) : $this->t('Revisions for %title', ['%title' => $news->label()]);

        $header = [$this->t('Revision'), $this->t('Operations')];
        $revert_permission = (($account->hasPermission("revert all news entity revisions") || $account->hasPermission('administer news entity entities')));
        $delete_permission = (($account->hasPermission("delete all news entity revisions") || $account->hasPermission('administer news entity entities')));

        $rows = [];

        $vids = $news_storage->revisionIds($news);

        $latest_revision = TRUE;

        foreach (array_reverse($vids) as $vid) {
            /** @var \Drupal\drug_news\NewsEntityInterface $revision */
            $revision = $news_storage->loadRevision($vid);
            // Only show revisions that are affected by the language that is being
            // displayed.
            if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
                $username = [
                    '#theme' => 'username',
                    '#account' => $revision->getRevisionUser(),
                ];

                // Use revision link to link to revisions that are not active.
                $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
                if ($vid != $news->getRevisionId()) {
                    $link = $this->l($date, new Url('entity.healthy.revision', [
                        'healthy' => $news->id(),
                        'healthy_revision' => $vid,
                    ]));
                } else {
                    $link = $news->link($date);
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
                } else {
                    $links = [];
                    if ($revert_permission) {
                        $links['revert'] = [
                            'title' => $this->t('Revert'),
                            'url' => $has_translations ?
                                Url::fromRoute('entity.healthy.translation_revert', [
                                    'healthy' => $news->id(),
                                    'healthy_revision' => $vid,
                                    'langcode' => $langcode,
                                ]) :
                                Url::fromRoute('entity.healthy.revision_revert', [
                                    'healthy' => $news->id(),
                                    'healthy_revision' => $vid,
                                ]),
                        ];
                    }

                    if ($delete_permission) {
                        $links['delete'] = [
                            'title' => $this->t('Delete'),
                            'url' => Url::fromRoute('entity.healthy.revision_delete', [
                                'healthy' => $news->id(),
                                'healthy_revision' => $vid,
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

        $build['healthy_revisions_table'] = [
            '#theme' => 'table',
            '#rows' => $rows,
            '#header' => $header,
        ];

        return $build;
    }

    /**
     * List.
     *
     * @return string
     *   Return Hello string.
     */
    public function list()
    {

        $tags = null;
        if (isset($_GET['tags'])) {
            $tags = $_GET['tags'];
        }
        $search = '';
        if (isset($_GET['keys'])) {
            $search = $_GET['keys'];
        }

        return [
            'results' => [
                '#theme' => 'healthy_list',
                '#healthies' => $this->getAllHealthy(),
                '#tags' => $this->getTags(),
                '#get' => $tags,
                '#search' => $search,
            ],
            'pager' => [
                '#type' => 'pager',
            ],
        ];
    }

    public function getAllHealthy()
    {
        $tid = False;
        if (isset($_GET['tags'])) {
            $tags = $_GET['tags'];
            $tid = $this->getTagsTid($tags);
        }

        if ($tid) {
            $ids = \Drupal::entityQuery('healthy')
                ->condition('status', 1)
                ->condition('field_tags', $tid)
                ->sort('field_weight','ASC')
                ->sort('created','DESC')
                ->pager(10)
                ->execute();
        } elseif (isset($_GET['keys'])) {
            $ids = \Drupal::entityQuery('healthy')
                ->condition('status', 1)
                ->condition('name', $_GET['keys'], 'CONTAINS')
                ->sort('field_weight','ASC')
                ->sort('created','DESC')
                ->pager(10)
                ->execute();
        } else {
            $ids = \Drupal::entityQuery('healthy')
                ->condition('status', 1)
                ->sort('field_weight','ASC')
                ->sort('created','DESC')
                ->pager(10)
                ->execute();

        }
        $result = Healthy::loadMultiple($ids);
        return $result;
    }

    public function getTags()
    {
        $tags = [];
        $ids = \Drupal::entityQuery('news')
            ->condition('status', 1)
            ->execute();
        $result = Healthy::loadMultiple($ids);
        foreach ($result as $drug) {
            foreach ($drug->get('field_tags')->getValue() as $tag) {
                $term = \Drupal\taxonomy\Entity\Term::load($tag['target_id']);
                $tags[$tag['target_id']] = $term->getName();
            }
        }
        return $tags;
    }

    public function getTagsTid($name)
    {
        $term = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties(['name' => $name, 'vid' => 'tags']);
        $term = reset($term);
        $term_id = $term->id();
        return $term_id;
    }
    public function title(){
        return ['#markup' => \Drupal::state()->get('/healthy','E-Healthy'), '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList()];

    }

}
