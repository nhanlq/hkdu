<?php

/**
 * @file
 * Contains drug_search.page.inc.
 *
 * Page callback for Drug search entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Drug search templates.
 *
 * Default template: drug_search.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_drug_search(array &$variables) {
  // Fetch DrugSearch Entity Object.
  $drug_search = $variables['elements']['#drug_search'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
