<?php

namespace Drupal\applenews\Normalizer;

class ApplenewsTextComponentNormalizer extends ApplenewsComponentNormalizerBase {

  protected $componentType = 'text';

  /**
   * {@inheritdoc}
   */
  public function normalize($data, $format = NULL, array $context = []) {
    $component_class = $this->getComponentClass($data['id']);
    $entity = $context['entity'];
    $view_mode = $context['view_mode'];

    $field_name = $data['component_data']['text'];
    $text = $entity->get($field_name)->view($view_mode);
    $component = new $component_class($this->renderer->renderRoot($text));

    $component->setFormat($data['component_data']['format']);
    $component->setLayout($this->getComponentLayout($data['component_layout']));

    return $component;
  }
}
