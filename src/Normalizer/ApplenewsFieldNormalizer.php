<?php

namespace Drupal\applenews\Normalizer;

use Drupal\Core\Field\FieldItemListInterface;

class ApplenewsFieldNormalizer extends ApplenewsNormalizerBase {
  /**
   * {@inheritdoc}
   */
  public function supportsNormalization($data, $format = NULL) {
    return $format === $this->format && $data instanceof FieldItemListInterface;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize($field, $format = NULL, array $context = []) {
    $value = '';
    $property = $context['field_property'];
    if ($property == 'base') {
      $value .= $field->value;
    }
    else {
      foreach ($field as $field_item) {
        $value .= $this->serializer->normalize($field_item->{$property});
      }
    }

    return $value;
  }
}
