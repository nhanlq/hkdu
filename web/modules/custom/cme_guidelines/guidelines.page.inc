<?php

/**
 * @file
 * Contains guidelines.page.inc.
 *
 * Page callback for Guidelines entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Guidelines templates.
 *
 * Default template: guidelines.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_guidelines(array &$variables) {
    // Fetch Guidelines Entity Object.
    $current_path = \Drupal::service('path.current')->getPath();
    $path = explode('/', $current_path);
    $id = $path[3];
    $event_org = \Drupal\cme_guidelines\Entity\Guidelines::load($id);
    $variables['cme_guidelines'] = $event_org;
    // Helpful $content variable for templates.
    foreach (Element::children($variables['elements']) as $key) {
        $variables['content'][$key] = $variables['elements'][$key];
    }
}
