<?php

namespace Drupal\applenews\Plugin\applenews\ComponentType;

use Drupal\applenews\Plugin\ApplenewsComponentTypeBase;

/**
 * Plugin class to generate all the default Component plugins.
 *
 * @ApplenewsComponentType(
 *  id = "default_nested",
 *  label = @Translation("Default Nested Component Type"),
 *  description = @Translation("Default component types based on AppleNewsAPI library."),
 *  component_type = "nested",
 *  deriver = "Drupal\applenews\Derivative\ApplenewsDefaultComponentNestedDeriver"
 * )
 */
class ApplenewsDefaultNestedComponentType extends ApplenewsComponentTypeBase {

}
