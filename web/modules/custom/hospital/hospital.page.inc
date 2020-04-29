<?php

/**
 * @file
 * Contains hospital.page.inc.
 *
 * Page callback for Hospital entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Hospital entity templates.
 *
 * Default template: hospital.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_hospital(array &$variables) {
  // Fetch HospitalEntity Entity Object.
  $hospital = $variables['elements']['#hospital'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
