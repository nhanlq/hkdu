<?php

/**
 * @file
 * Contains score.page.inc.
 *
 * Page callback for Score entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Score templates.
 *
 * Default template: score.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_score(array &$variables) {
  // Fetch Score Entity Object.
  $score = $variables['elements']['#score'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
