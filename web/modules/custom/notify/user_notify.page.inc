<?php

/**
 * @file
 * Contains user_notify.page.inc.
 *
 * Page callback for User notify entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for User notify templates.
 *
 * Default template: user_notify.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_user_notify(array &$variables) {
  // Fetch UserNotify Entity Object.
  $user_notify = $variables['elements']['#user_notify'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
