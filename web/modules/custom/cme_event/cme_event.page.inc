<?php

/**
 * @file
 * Contains cme_event.page.inc.
 *
 * Page callback for CME Event entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for CME Event templates.
 *
 * Default template: cme_event.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_cme_event(array &$variables) {
  // Fetch CmeEvent Entity Object.
  $cme_event = $variables['elements']['#cme_event'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
