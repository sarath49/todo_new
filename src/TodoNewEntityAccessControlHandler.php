<?php

namespace Drupal\todo_new;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Todo new entity entity.
 *
 * @see \Drupal\todo_new\Entity\TodoNewEntity.
 */
class TodoNewEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\todo_new\Entity\TodoNewEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished todo new entity entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published todo new entity entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit todo new entity entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete todo new entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add todo new entity entities');
  }

}
