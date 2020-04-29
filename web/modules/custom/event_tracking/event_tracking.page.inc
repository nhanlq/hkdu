<?php

/**
 * @file
 * Contains event_tracking.page.inc.
 *
 * Page callback for Event tracking entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Event tracking templates.
 *
 * Default template: event_tracking.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_event_tracking(array &$variables) {
  // Fetch EventTracking Entity Object.
  $event_tracking = $variables['elements']['#event_tracking'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
