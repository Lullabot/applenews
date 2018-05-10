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

    $field_name = $data['component_data']['URL']['field_name'];
    $context['field_property'] = $data['component_data']['URL']['field_property'];
    $text = $this->serializer->normalize($entity->get($field_name), $format, $context);
    $component = new $component_class($text);

    $field_name = $data['component_data']['caption']['field_name'];
    $context['field_property'] = $data['component_data']['caption']['field_property'];
    $text = $this->serializer->normalize($entity->get($field_name), $format, $context);
    $component->setCaption($text);
    $component->setLayout($this->getComponentLayout($data['component_layout']));

    return $component;
  }
}
