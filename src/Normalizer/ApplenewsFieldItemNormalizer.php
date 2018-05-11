<?php

namespace Drupal\applenews\Normalizer;

use Drupal\Core\Field\FieldItemInterface;

class ApplenewsFieldItemNormalizer extends ApplenewsNormalizerBase {
  /**
   * {@inheritdoc}
   */
  public function supportsNormalization($data, $format = NULL) {
    return $format === $this->format && $data instanceof FieldItemInterface;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize($field_item, $format = NULL, array $context = []) {
    $property = $context['field_property'];
    if ($property == 'base') {
      $value = $field_item->value;
    }
    else {
        $value = $this->serializer->normalize($field_item->{$property});
    }

    return $value;
  }
}
