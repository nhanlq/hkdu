<?php

/**
 * @file
 * Contains doctor.page.inc.
 *
 * Page callback for Doctor entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Doctor entity templates.
 *
 * Default template: doctor.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_doctor(array &$variables) {
  // Fetch DoctorEntity Entity Object.
  $doctor = $variables['elements']['#doctor'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
