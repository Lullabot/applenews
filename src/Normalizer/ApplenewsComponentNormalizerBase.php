<?php

namespace Drupal\applenews\Normalizer;

use Drupal\applenews\Plugin\ApplenewsComponentTypeManager;
use Drupal\Core\Render\RendererInterface;

abstract class ApplenewsComponentNormalizerBase extends ApplenewsNormalizerBase {

  /**
   * The component type of the plugin. This is used in ::supportsNormalization().
   *
   * @see \Drupal\applenews\Annotation\ApplenewsComponentType
   *
   * @var string
   */
  protected $componentType;

  /**
   * @var ApplenewsComponentTypeManager
   */
  protected $applenewsComponentTypeManager;

  protected $renderer;

  /**
   * Constructs a normalizer object.
   *
   * @param ApplenewsComponentTypeManager $component_type_manager
   * @param RendererInterface $renderer
   */
  public function __construct(ApplenewsComponentTypeManager $component_type_manager, RendererInterface $renderer) {
    $this->applenewsComponentTypeManager = $component_type_manager;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public function supportsNormalization($data, $format = NULL) {
    // Only consider this normalizer if we are trying to normalize a content
    // entity into the 'applenews' format and the component is of type "text"
    if ($format === $this->format && is_array($data) && isset($data['id'])) {
      $component = $this->applenewsComponentTypeManager->createInstance($data['id']);
      $type = $component->getComponentType();
      return $component->getComponentType() == $this->componentType;
    }

    return FALSE;
  }

  protected function getComponentClass($plugin_id) {
    $component = $this->applenewsComponentTypeManager->createInstance($plugin_id);
    return $component->getComponentClass();
  }
}
