<?php

/**
 * @file
 * Contains gallery.page.inc.
 *
 * Page callback for Gallery entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Gallery templates.
 *
 * Default template: gallery.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_gallery(array &$variables) {
  // Fetch Gallery Entity Object.
  $gallery = $variables['elements']['#gallery'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
