<?php

namespace Drupal\todo_new;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Todo new entity entities.
 *
 * @ingroup todo_new
 */
class TodoNewEntityListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Todo new entity ID');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\todo_new\Entity\TodoNewEntity */
    $row['id'] = $entity->id();
    return $row + parent::buildRow($entity);
  }

}
