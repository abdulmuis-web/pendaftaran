<?php

namespace Drupal\pendaftaran\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\pendaftaran\Entity\PendaftaranInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
/**
 * Class PendaftaranController.
 *
 *  Returns responses for Pendaftaran routes.
 */
class PendaftaranController extends ControllerBase implements ContainerInjectionInterface {
  
  protected $pendaftaranService;
  
  /**
   * Class constructor.
   */
  public function __construct($pendaftaranService) {
    $this->pendaftaranService = $pendaftaranService;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('pendaftaran.pendaftaran_service')
    );
  }
  
  /**
   * Generates an example page.
   */
  public function pendaftaran() {
    return array(
      '#markup' => t('Hello @value!', array('@value' => $this->pendaftaranService->getPendaftaranValue())),
    );
  }	
	

  /**
   * Displays a Pendaftaran  revision.
   *
   * @param int $pendaftaran_revision
   *   The Pendaftaran  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($pendaftaran_revision) {
    $pendaftaran = $this->entityManager()->getStorage('pendaftaran')->loadRevision($pendaftaran_revision);
    $view_builder = $this->entityManager()->getViewBuilder('pendaftaran');

    return $view_builder->view($pendaftaran);
  }

  /**
   * Page title callback for a Pendaftaran  revision.
   *
   * @param int $pendaftaran_revision
   *   The Pendaftaran  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($pendaftaran_revision) {
    $pendaftaran = $this->entityManager()->getStorage('pendaftaran')->loadRevision($pendaftaran_revision);
    return $this->t('Revision of %title from %date', ['%title' => $pendaftaran->label(), '%date' => format_date($pendaftaran->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Pendaftaran .
   *
   * @param \Drupal\pendaftaran\Entity\PendaftaranInterface $pendaftaran
   *   A Pendaftaran  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(PendaftaranInterface $pendaftaran) {
    $account = $this->currentUser();
    $langcode = $pendaftaran->language()->getId();
    $langname = $pendaftaran->language()->getName();
    $languages = $pendaftaran->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $pendaftaran_storage = $this->entityManager()->getStorage('pendaftaran');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $pendaftaran->label()]) : $this->t('Revisions for %title', ['%title' => $pendaftaran->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all pendaftaran revisions") || $account->hasPermission('administer pendaftaran entities')));
    $delete_permission = (($account->hasPermission("delete all pendaftaran revisions") || $account->hasPermission('administer pendaftaran entities')));

    $rows = [];

    $vids = $pendaftaran_storage->revisionIds($pendaftaran);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\pendaftaran\PendaftaranInterface $revision */
      $revision = $pendaftaran_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $pendaftaran->getRevisionId()) {
          $link = $this->l($date, new Url('entity.pendaftaran.revision', ['pendaftaran' => $pendaftaran->id(), 'pendaftaran_revision' => $vid]));
        }
        else {
          $link = $pendaftaran->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
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
              'url' => Url::fromRoute('entity.pendaftaran.revision_revert', ['pendaftaran' => $pendaftaran->id(), 'pendaftaran_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.pendaftaran.revision_delete', ['pendaftaran' => $pendaftaran->id(), 'pendaftaran_revision' => $vid]),
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

    $build['pendaftaran_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
