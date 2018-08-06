<?php

namespace Drupal\applenews;

use Drupal\applenews\Entity\ApplenewsArticle;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\Serializer\Serializer;

/**
 * Applenews manager contains common functions to manage fields.
 */
class ApplenewsManager {
  use StringTranslationTrait;

  /**
   * The entity manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The applenews settings config object.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * The serializer.
   *
   * @var \Symfony\Component\Serializer\SerializerInterface
   */
  protected $serializer;

  /**
   * The date formatter service.
   *
   * @var \Drupal\applenews\PublisherInterface
   */
  protected $publisher;

  /**
   * ApplenewsManager constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The string translation service.
   * @param \Symfony\Component\Serializer\Serializer $serializer
   *   Serializer.
   * @param \Drupal\applenews\PublisherInterface $publisher
   *   Apple news publisher.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager,EntityFieldManagerInterface $entity_field_manager, ConfigFactoryInterface $config_factory, TranslationInterface $string_translation, Serializer $serializer, PublisherInterface $publisher) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->config = $config_factory->get('applenews.settings');
    $this->stringTranslation = $string_translation;
    $this->serializer = $serializer;
    $this->publisher = $publisher;
  }

  /**
   * {@inheritdoc}
   */
  public function getFields($entity_type_id) {
    $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
    if (!$entity_type->entityClassImplements(FieldableEntityInterface::class)) {
      return [];
    }

    $map = $this->entityFieldManager->getFieldMapByFieldType('applenews_default');
    return isset($map[$entity_type_id]) ? $map[$entity_type_id] : [];
  }

  /**
   * Post article to selected channels with given template.
   *
   * Using single method here for create and update as it is possible from
   * entity (e.g. node) UI to create an entity without publishing to Apple News
   * and decide to publish on entity update.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *
   * @return bool
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function postArticle(EntityInterface $entity) {
    $fields = $this->getFields($entity->getEntityTypeId());
    if (!$fields){
      return FALSE;
    }

    foreach ($fields as $field_name => $detail) {
      $field = $entity->get($field_name);
      if ($field->status) {

        $template = $field->template;
        $channels = unserialize($field->channels);
        $document = $this->getDocumentDataFromEntity($entity, $template);
        $data = [
          'json' => $document,
          // 'files' => ''
        ];
        foreach ($channels as $channel_id => $sections) {
          // Publish for the first time.
          if (!$field->article) {
            $data['metadata'] = $this->getMetaData($sections);
            $response = $this->doPost($channel_id, $data);
            $article = ApplenewsArticle::create([
              'entity_id' => $entity->id(),
              'entity_type' => $entity->getEntityType()->id(),
              'field_name' => $field_name,
            ]);
            $article->updateFromResponse($response)->save();

          }
          else {
            /** @var ApplenewsArticle $article */
            $article = $field->article;
            // hook_entity_update get called on ->save(). Avoid multiple calls.
            $data['metadata'] = $this->getMetaData($sections, $article->getRevision());
            $response = $this->doUpdate($article->getArticleId(), $data);
            $article->updateFromResponse($response)->save();
          }
        }
      }
    }

    return TRUE;
  }

  /**
   * Fetches metadata.
   *
   * @param $sections
   *   An array of section ids.
   * @param null|string $revision_id
   *  Revision ID for article update.
   *
   * @return string
   */
  protected function getMetadata($sections, $revision_id = NULL) {
    foreach ($sections as $section_id => $flag) {
      $section_urls[] =   $this->config->get('endpoint') . '/sections/' . $section_id;
    }
    $data = [
      'links' => [
        'sections' => $section_urls,
      ]
    ];
    if ($revision_id) {
      $data['revision'] = $revision_id;
    }
    return json_encode(['data' => $data], JSON_UNESCAPED_SLASHES);
  }


  /**
   * @param \Drupal\Core\Entity\EntityInterface $entity
   * @param $field_name
   *
   * @return \Drupal\applenews\Entity\ApplenewsArticle|null
   */
  public static function getArticle(EntityInterface $entity, $field_name) {
    try {
      $query = \Drupal::entityQuery('applenews_article');

      $ids = $query
        ->condition('entity_type', $entity->getEntityType()->id())
        ->condition('entity_id', $entity->id())
        ->condition('field_name', $field_name)
        ->execute();
      return \Drupal::entityTypeManager()->getStorage('applenews_article')->load(current($ids));
    }
    catch (\Exception $e) {
      // Do not throw exception.
    }

    return NULL;
  }

  /**
   * Update article.
   *
   * @param $article_id
   * @param $data
   *
   * @return mixed
   */
  protected function doUpdate($article_id, $data) {
    return $this->publisher->updateArticle($article_id, $data);
  }

  /**
   * Posts article.
   *
   * @param $channel_id
   * @param $data
   *
   * @return mixed
   */
  protected function doPost($channel_id, $data) {
    return $this->publisher->postArticle($channel_id, $data);
  }

  /**
   * Generates document from entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   * @param $template
   *
   * @return string
   */
  protected function getDocumentDataFromEntity(EntityInterface $entity, $template) {
    $context['template_id'] = $template;
    /** @var \ChapterThree\AppleNewsAPI\Document $document */
    $document = $this->serializer->normalize($entity, 'applenews', $context);
    return Json::encode($document);
  }

}
