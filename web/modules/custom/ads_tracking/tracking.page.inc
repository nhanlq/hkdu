<?php

/**
 * @file
 * Contains tracking.page.inc.
 *
 * Page callback for Tracking entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Tracking templates.
 *
 * Default template: tracking.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_tracking(array &$variables) {
  // Fetch Tracking Entity Object.
  $tracking = $variables['elements']['#tracking'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
