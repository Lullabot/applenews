<?php

namespace Drupal\applenews\Plugin\Field\FieldType;

use Drupal\comment\CommentManagerInterface;
use Drupal\comment\Entity\CommentType;
use Drupal\comment\Plugin\Field\FieldType\CommentItemInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\UrlGeneratorTrait;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Session\AnonymousUserSession;

/**
 * Plugin implementation of the 'comment' field type.
 *
 * @FieldType(
 *   id = "applenews_template_default",
 *   label = @Translation("Applenews template"),
 *   description = @Translation("This field manages configuration and presentation of applenews template."),
 *   default_widget = "applenews_template_default",
 *   cardinality = 1,
 * )
 */
class ApplenewsTemplate extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['status'] = DataDefinition::create('boolean')
      ->setLabel(t('Boolean value'));
    $properties['template'] = DataDefinition::create('string')
      ->setLabel(t('Template'));
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'status' => [
          'description' => 'Enable applenews',
          'type' => 'int',
          'default' => 0,
        ],
        'template' => [
          'description' => 'Template name',
          'type' => 'varchar',
          'length' => 128,
        ],
      ],
      'indexes' => [],
      'foreign keys' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName() {
    return 'status';
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    return [
      'status' => mt_rand(0, 1)
    ];
  }

}
