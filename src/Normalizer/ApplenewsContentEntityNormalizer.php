<?php

namespace Drupal\applenews\Normalizer;

use ChapterThree\AppleNewsAPI\Document;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Applenews content entity normalizer. Takes a content entity, normalizes it
 * into a ChapterThree\AppleNewsAPI\Document.
 */
class ApplenewsContentEntityNormalizer extends ApplenewsNormalizerBase {

  /**
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs an ApplenewsTemplateSelection object.
   *
   * @param EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function supportsNormalization($data, $format = NULL) {
    // Only consider this normalizer if we are trying to normalize a content
    // entity into the 'applenews' format.
    return $format === $this->format && $data instanceof ContentEntityInterface;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize($data, $format = NULL, array $context = []) {
    // @todo check cache
    $template = $this->entityTypeManager->getStorage('applenews_template')->load($context['template_id']);
    $layout = new Document\Layouts\Layout($template->columns, $template->width);
    $document = new Document($data->uuid(), $data->getTitle(), $data->language()->getId(), $layout);

    $context['entity'] = $data;
    foreach ($template->getComponents() as $component) {
      $document->addComponent($this->serializer->normalize($component, $format, $context));
    }
    return $document->jsonSerialize();
  }
}
