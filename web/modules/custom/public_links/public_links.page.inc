<?php

/**
 * @file
 * Contains public_links.page.inc.
 *
 * Page callback for Public links entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Public links templates.
 *
 * Default template: public_links.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_public_links(array &$variables) {
  // Fetch PublicLinks Entity Object.
  $public_links = $variables['elements']['#public_links'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
