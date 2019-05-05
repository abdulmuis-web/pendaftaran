<?php

/**
 * @file
 * Contains pendaftaran.page.inc.
 *
 * Page callback for Pendaftaran entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Pendaftaran templates.
 *
 * Default template: pendaftaran.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_pendaftaran(array &$variables) {
  // Fetch Pendaftaran Entity Object.
  $pendaftaran = $variables['elements']['#pendaftaran'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
