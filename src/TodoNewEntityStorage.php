<?php

namespace Drupal\todo_new;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\todo_new\Entity\TodoNewEntityInterface;

/**
 * Defines the storage handler class for Todo new entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Todo new entity entities.
 *
 * @ingroup todo_new
 */
class TodoNewEntityStorage extends SqlContentEntityStorage implements TodoNewEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(TodoNewEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {todo_new_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {todo_new_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(TodoNewEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {todo_new_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('todo_new_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
