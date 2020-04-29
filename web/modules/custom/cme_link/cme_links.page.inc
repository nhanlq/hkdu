<?php

/**
 * @file
 * Contains cme_links.page.inc.
 *
 * Page callback for CME Links entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for CME Links templates.
 *
 * Default template: cme_links.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_cme_links(array &$variables) {
  // Fetch CmeLinks Entity Object.
  $cme_links = $variables['elements']['#cme_links'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
