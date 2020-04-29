<?php

/**
 * @file
 * Contains drug_news.page.inc.
 *
 * Page callback for Drug news entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Drug news templates.
 *
 * Default template: drug_news.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_drug_news(array &$variables) {
  // Fetch DrugNews Entity Object.
  $drug_news = $variables['elements']['#drug_news'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
