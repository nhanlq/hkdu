<?php

/**
 * @file
 * Contains tools.page.inc.
 *
 * Page callback for Tools entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Tools templates.
 *
 * Default template: tools.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_tools(array &$variables) {
  // Fetch Tools Entity Object.
  $tools = $variables['elements']['#tools'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
