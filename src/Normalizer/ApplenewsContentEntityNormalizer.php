<?php

namespace Drupal\applenews\Normalizer;

use ChapterThree\AppleNewsAPI\Document;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Applenews content entity normalizer. Takes a content entity, normalizes it
 * into a ChapterThree\AppleNewsAPI\Document.
 */
class ApplenewsContentEntityNormalizer extends ApplenewsNormalizerBase {

  /**
   * {@inheritdoc}
   */
  public function supportsNormalization($data, $format = NULL) {
    // Only consider this normalizer if we are trying to normalize a content
    // entity into the 'fbia' format.
    return $format === $this->format && $data instanceof ContentEntityInterface;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize($data, $format = NULL, array $context = []) {
    // @todo check cache
    // @todo grab template connected to this entity type and get layout.
    $template = '';
    $layout = new Document\Layouts\Layout(10, 1024);
    $document = new Document($data->uuid(), $data->getTitle(), $data->language(), $layout);

    // @todo grab template and get list of components. Loop through and serialize them, adding results to document here.
    $context['entity'] = $data;
    foreach ($template->components as $component) {
      $document->addComponent($this->serializer->normalize($component, $format, $context));
    }
    return $document;
  }
}
