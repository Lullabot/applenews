<?php

namespace Drupal\applenews\Normalizer;

use ChapterThree\AppleNewsAPI\Document\Layouts\ComponentLayout;
use ChapterThree\AppleNewsAPI\Document\Margin;
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

  /**
   * Constructs a normalizer object.
   *
   * @param ApplenewsComponentTypeManager $component_type_manager
   */
  public function __construct(ApplenewsComponentTypeManager $component_type_manager) {
    $this->applenewsComponentTypeManager = $component_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function supportsNormalization($data, $format = NULL) {
    // Only consider this normalizer if we are trying to normalize a content
    // entity into the 'applenews' format and the component is of type "text"
    if ($format === $this->format && is_array($data) && isset($data['id'])) {
      $component = $this->applenewsComponentTypeManager->createInstance($data['id']);
      return $component->getComponentType() == $this->componentType;
    }

    return FALSE;
  }

  protected function getComponentClass($plugin_id) {
    $component = $this->applenewsComponentTypeManager->createInstance($plugin_id);
    return $component->getComponentClass();
  }

  protected function getComponentLayout($component_layout) {
    $layout = new ComponentLayout();
    $layout->setColumnSpan($component_layout['column_span']);
    $layout->setColumnStart($component_layout['column_start']);
    $layout->setMargin(new Margin($component_layout['margin_top'], $component_layout['margin_bottom']));
    return $layout;
  }
}
