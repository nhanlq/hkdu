<?php

/**
 * @file
 * Contains point.page.inc.
 *
 * Page callback for Point entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Point templates.
 *
 * Default template: point.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_point(array &$variables) {
  // Fetch Point Entity Object.
  $point = $variables['elements']['#point'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
