<?php

namespace Drupal\applenews\Plugin\applenews\ComponentType;

use Drupal\applenews\Plugin\ApplenewsComponentTypeBase;

/**
 * Plugin class to generate all the default Component plugins.
 *
 * @ApplenewsComponentType(
 *  id = "default",
 *  label = @Translation("Default Component Type"),
 *  description = @Translation("Default component types based on AppleNewsAPI library."),
 *  deriver = "Drupal\applenews\Derivative\ApplenewsDefaultComponentTypeDeriver"
 * )
 */
class ApplenewsDefaultComponentType extends ApplenewsComponentTypeBase {

}
