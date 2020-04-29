<?php

/**
 * @file
 * Contains download.page.inc.
 *
 * Page callback for Download entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Download templates.
 *
 * Default template: download.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_download(array &$variables) {
  // Fetch Download Entity Object.
  $download = $variables['elements']['#download'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
