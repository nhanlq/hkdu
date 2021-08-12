<?php

/**
 * @file
 * Contains notify.page.inc.
 *
 * Page callback for Notify entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Notify templates.
 *
 * Default template: notify.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_notify(array &$variables) {
  // Fetch Notify Entity Object.
  $notify = $variables['elements']['#notify'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
