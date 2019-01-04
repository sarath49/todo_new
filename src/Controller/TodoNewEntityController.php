<?php

namespace Drupal\todo_new\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\todo_new\Entity\TodoNewEntityInterface;

/**
 * Class TodoNewEntityController.
 *
 *  Returns responses for Todo new entity routes.
 */
class TodoNewEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Todo new entity  revision.
   *
   * @param int $todo_new_entity_revision
   *   The Todo new entity  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($todo_new_entity_revision) {
    $todo_new_entity = $this->entityManager()->getStorage('todo_new_entity')->loadRevision($todo_new_entity_revision);
    $view_builder = $this->entityManager()->getViewBuilder('todo_new_entity');

    return $view_builder->view($todo_new_entity);
  }

  /**
   * Page title callback for a Todo new entity  revision.
   *
   * @param int $todo_new_entity_revision
   *   The Todo new entity  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($todo_new_entity_revision) {
    $todo_new_entity = $this->entityManager()->getStorage('todo_new_entity')->loadRevision($todo_new_entity_revision);
    return $this->t('Revision of %title from %date', ['%title' => $todo_new_entity->label(), '%date' => format_date($todo_new_entity->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Todo new entity .
   *
   * @param \Drupal\todo_new\Entity\TodoNewEntityInterface $todo_new_entity
   *   A Todo new entity  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(TodoNewEntityInterface $todo_new_entity) {
    $account = $this->currentUser();
    $langcode = $todo_new_entity->language()->getId();
    $langname = $todo_new_entity->language()->getName();
    $languages = $todo_new_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $todo_new_entity_storage = $this->entityManager()->getStorage('todo_new_entity');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $todo_new_entity->label()]) : $this->t('Revisions for %title', ['%title' => $todo_new_entity->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all todo new entity revisions") || $account->hasPermission('administer todo new entity entities')));
    $delete_permission = (($account->hasPermission("delete all todo new entity revisions") || $account->hasPermission('administer todo new entity entities')));

    $rows = [];

    $vids = $todo_new_entity_storage->revisionIds($todo_new_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\todo_new\TodoNewEntityInterface $revision */
      $revision = $todo_new_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $todo_new_entity->getRevisionId()) {
          $link = $this->l($date, new Url('entity.todo_new_entity.revision', ['todo_new_entity' => $todo_new_entity->id(), 'todo_new_entity_revision' => $vid]));
        }
        else {
          $link = $todo_new_entity->link($date);
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
              'url' => $has_translations ?
              Url::fromRoute('entity.todo_new_entity.translation_revert', ['todo_new_entity' => $todo_new_entity->id(), 'todo_new_entity_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.todo_new_entity.revision_revert', ['todo_new_entity' => $todo_new_entity->id(), 'todo_new_entity_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.todo_new_entity.revision_delete', ['todo_new_entity' => $todo_new_entity->id(), 'todo_new_entity_revision' => $vid]),
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

    $build['todo_new_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
