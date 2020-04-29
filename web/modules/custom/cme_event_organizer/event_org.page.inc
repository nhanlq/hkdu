<?php

/**
 * @file
 * Contains event_org.page.inc.
 *
 * Page callback for Event Organizer entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Event Organizer templates.
 *
 * Default template: event_org.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_event_org(array &$variables) {
    // Fetch EventOrg Entity Object.
    $current_path = \Drupal::service('path.current')->getPath();
    $path = explode('/', $current_path);
    $id = $path[3];
    $event_org = \Drupal\cme_event_organizer\Entity\EventOrg::load($id);
    $variables['event_org'] = $event_org;
    // Helpful $content variable for templates.
    foreach (Element::children($variables['elements']) as $key) {
        $variables['content'][$key] = $variables['elements'][$key];
    }
}
