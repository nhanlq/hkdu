<?php

/**
 * @file
 * Contains pharm_dir.page.inc.
 *
 * Page callback for Pharm dir entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Pharm dir templates.
 *
 * Default template: pharm_dir.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_pharm_dir(array &$variables) {
  // Fetch PharmDir Entity Object.
  $pharm_dir = $variables['elements']['#pharm_dir'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
