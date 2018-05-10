<?php

namespace Drupal\applenews\Normalizer;

class ApplenewsImageComponentNormalizer extends ApplenewsComponentNormalizerBase {

  protected $componentType = 'image';

  /**
   * {@inheritdoc}
   */
  public function normalize($data, $format = NULL, array $context = []) {
    $component_class = $this->getComponentClass($data['id']);
    $entity = $context['entity'];
    $view_mode = $context['view_mode'];

    $field_name = $data['component_data']['URL'];
    $url = $entity->get($field_name);
    $component = new $component_class($this->renderer->renderRoot($url));

    $caption = $entity->get($data['component_data']['caption'])->view($view_mode);
    $component->setCaption($this->renderer->renderRoot($caption));
    $component->setLayout($this->getComponentLayout($data['component_layout']));

    return $component;
  }
}
