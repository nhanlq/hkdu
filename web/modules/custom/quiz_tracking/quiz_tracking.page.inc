<?php

/**
 * @file
 * Contains quiz_tracking.page.inc.
 *
 * Page callback for Quiz tracking entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Quiz tracking templates.
 *
 * Default template: quiz_tracking.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_quiz_tracking(array &$variables) {
  // Fetch QuizTracking Entity Object.
  $quiz_tracking = $variables['elements']['#quiz_tracking'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
