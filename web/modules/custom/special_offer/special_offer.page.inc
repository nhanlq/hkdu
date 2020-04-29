<?php

/**
 * @file
 * Contains special_offer.page.inc.
 *
 * Page callback for Special offer entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Special offer templates.
 *
 * Default template: special_offer.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_special_offer(array &$variables) {
  // Fetch SpecialOffer Entity Object.
  $special_offer = $variables['elements']['#special_offer'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
