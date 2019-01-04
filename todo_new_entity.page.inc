<?php

/**
 * @file
 * Contains todo_new_entity.page.inc.
 *
 * Page callback for Todo new entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Todo new entity templates.
 *
 * Default template: todo_new_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_todo_new_entity(array &$variables) {
  // Fetch TodoNewEntity Entity Object.
  $todo_new_entity = $variables['elements']['#todo_new_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
