<?php

/**
 * @file
 * Contains media_entity.page.inc.
 *
 * Page callback for Media entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Media entity templates.
 *
 * Default template: media_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_media_entity(array &$variables) {
  // Fetch MediaEntity Entity Object.
  $media_entity = $variables['elements']['#media_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
