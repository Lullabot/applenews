<?php

namespace Drupal\applenews\Derivative;

/**
 *
 */
interface ApplenewsDefaultDeriverInterface {

  /**
   * Get the list of Apple News component types with their underlying class
   * from the AppleNewsAPI. @see https://github.com/chapter-three/AppleNewsAPI.
   *
   * @return array
   *   An array keyed by the "role" of the Apple News component, and containing
   *   the following:
   *    - component_class - the fully-qualified name of the AppleNewsAPI class
   *    - label
   *    - description
   */
  public function getComponentClasses();

}
