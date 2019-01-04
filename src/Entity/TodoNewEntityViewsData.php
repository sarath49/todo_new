<?php

namespace Drupal\todo_new\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Todo new entity entities.
 */
class TodoNewEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
