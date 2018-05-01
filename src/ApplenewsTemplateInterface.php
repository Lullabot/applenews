<?php

namespace Drupal\applenews;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface ApplenewsTemplateInterface extends ConfigEntityInterface {

  /**
   * Get the layout values for this template.
   *
   * @return array
   *  An associative array of the Apple News layout values.
   *
   * @see https://developer.apple.com/library/content/documentation/General/Conceptual/Apple_News_Format_Ref/Layout.html#//apple_ref/doc/uid/TP40015408-CH65-SW1
   */
  public function getLayout();

  /**
   * Get the list of components in this template.
   *
   * @return array
   *  An array of Component
   */
  public function getComponents();

  /**
   * @param array $component
   *  An array representing a component config object. @see applenews.schema.yml
   */
  public function addComponent(array $component);
}
