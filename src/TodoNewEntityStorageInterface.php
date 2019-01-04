<?php

namespace Drupal\todo_new;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface TodoNewEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Todo new entity revision IDs for a specific Todo new entity.
   *
   * @param \Drupal\todo_new\Entity\TodoNewEntityInterface $entity
   *   The Todo new entity entity.
   *
   * @return int[]
   *   Todo new entity revision IDs (in ascending order).
   */
  public function revisionIds(TodoNewEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Todo new entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Todo new entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\todo_new\Entity\TodoNewEntityInterface $entity
   *   The Todo new entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(TodoNewEntityInterface $entity);

  /**
   * Unsets the language for all Todo new entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
