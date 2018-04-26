<?php

namespace Drupal\applenews\Normalizer;

use ChapterThree\AppleNewsAPI\Document\Components\Component;

class ApplenewsComponentNormalizer extends ApplenewsNormalizerBase {

  /**
   * {@inheritdoc}
   *
   * @todo this should get our custom component plugin type, so check for that.
   */
  public function supportsNormalization($data, $format = NULL) {
    // Only consider this normalizer if we are trying to normalize a content
    // entity into the 'fbia' format.
    return $format === $this->format && is_array($data);
  }

  /**
   * {@inheritdoc}
   */
  public function normalize($data, $format = NULL, array $context = []) {
    // @todo get data from the appropriate field and component type.
    $component_type = $data->getComponentClass();
    $field_data = $context['entity']->get($data['field_name']);
    $component = new $component_type($field_data);

    return $component;
  }

}
