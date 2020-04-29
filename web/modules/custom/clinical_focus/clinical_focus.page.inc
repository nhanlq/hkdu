<?php

/**
 * @file
 * Contains clinical_focus.page.inc.
 *
 * Page callback for Clinical focus entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Clinical focus templates.
 *
 * Default template: clinical_focus.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_clinical_focus(array &$variables) {
  // Fetch ClinicalFocus Entity Object.
  $clinical_focus = $variables['elements']['#clinical_focus'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
