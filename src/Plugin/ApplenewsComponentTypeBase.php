<?php

namespace Drupal\applenews\Plugin;

use Drupal\applenews\ApplenewsFieldSelectionHelper;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\FieldStorageConfigInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ApplenewsComponentTypeBase extends PluginBase implements ApplenewsComponentTypeInterface {

  /**
   * @var EntityFieldManagerInterface
   */
  protected $fieldManager;

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = [];

    $element['component_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Adding @component_name component', ['@component_name' => $this->label()]),
      '#tree' => TRUE,
    ];

    $element['component_settings']['component_layout'] = [
      '#type' => 'details',
      '#title' => $this->t('Component Layout'),
      '#open' => TRUE,
    ];

    $element['component_settings']['component_layout']['column_start'] = [
      '#type' => 'number',
      '#title' => $this->t('Column Start'),
      '#description' => $this->t("Indicates which column the component's start position is in, based on the number of columns in the document or parent container. By default, the component will start in the first column (note that the first column is 0, not 1)."),
      '#default_value' => 0,
    ];

    $element['component_settings']['component_layout']['column_span'] = [
      '#type' => 'number',
      '#title' => $this->t('Column Span'),
      '#description' => $this->t("Indicates how many columns the component spans, based on the number of columns in the document. By default, the component spans the entire width of the document or the width of its container component."),
    ];

    $element['component_settings']['component_layout']['margin_top'] = [
      '#type' => 'number',
      '#title' => $this->t('Margin Top'),
      '#description' => $this->t('The margin for the top of this component.'),
      '#default_value' => 0,
    ];

    $element['component_settings']['component_layout']['margin_bottom'] = [
      '#type' => 'number',
      '#title' => $this->t('Margin Bottom'),
      '#description' => $this->t('The margin for the bottom of this component.'),
      '#default_value' => 0,
    ];

    $element['component_settings']['component_layout']['ignore_margin'] = [
      '#type' => 'select',
      '#title' => $this->t('Ignore Document Margin'),
      '#description' => $this->t('Indicates whether a document\'s margins should be respected or ignored by the parent container.'),
      '#options' => [
        'none' => $this->t('None'),
        'left' => $this->t('Left'),
        'right' => $this->t('Right'),
        'both' => $this->t('Both'),
      ]
    ];

    $element['component_settings']['component_layout']['ignore_gutter'] = [
      '#type' => 'select',
      '#title' => $this->t('Ignore Document Gutter'),
      '#description' => $this->t('Indicates whether the gutters (if any) to the left and right of the component should be ignored.'),
      '#options' => [
        'none' => $this->t('None'),
        'left' => $this->t('Left'),
        'right' => $this->t('Right'),
        'both' => $this->t('Both'),
      ]
    ];


    $element['component_settings']['id'] = [
      '#type' => 'hidden',
      '#value' => $this->pluginId,
    ];

    $element['component_settings']['component_data'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Component Data'),
      '#prefix' => '<div id="component-field-mapping-properties-wrapper">',
      '#suffix' => '</div>',
    ];

    // @todo add more component layout form elements

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->pluginDefinition['description'];
  }

  /**
   * {@inheritdoc}
   */
  public function getComponentClass() {
    return $this->pluginDefinition['component_class'];
  }

  public function getComponentType() {
    return $this->pluginDefinition['component_type'];
  }

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityFieldManagerInterface $field_manager) {
    $this->fieldManager = $field_manager;
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_field.manager')
    );
  }

  protected function getFieldOptions($node_type) {
    $fields = $this->fieldManager->getFieldDefinitions('node', $node_type);
    $field_options = [];
    $available_base_fields = $this->getBaseFields();
    foreach ($fields as $field_name => $field) {
      if ($field->getFieldStorageDefinition()->isBaseField() && in_array($field_name, $available_base_fields)) {
        $field_options[$field_name] = $field->getLabel();
      }
      elseif (!$field->getFieldStorageDefinition()->isBaseField()) {
        $field_options[$field_name] = $field->getLabel() . ' (' . $field->getType() . ')';
      }
    }

    return $field_options;
  }

  /**
   * Get field machine names of base fields that are availabe to use for content.
   *
   * @return array
   */
  protected function getBaseFields() {
    return [
      'title',
      'created',
      'changed',
    ];
  }

  protected function getFieldSelectionElement(FormStateInterface $form_state, $name, $label) {
    $input = $form_state->getUserInput();
    $node_type = $input['node_type'];

    $field_options = $this->getFieldOptions($node_type);
    $default_field = current(array_keys($field_options));

    $triggering_element = $form_state->getTriggeringElement();
    $field_selection_name = 'component_settings[component_data]['. $name . '][field_name]';
    if (isset($triggering_element) && $triggering_element['#name'] == $field_selection_name) {
      $default_field = $triggering_element['#value'];
    }

    if (isset($input['component_settings']['component_data'][$name]['field_name'])) {
      $default_field = $input['component_settings']['component_data'][$name]['field_name'];
    }

    $default_field_config = FieldConfig::loadByName('node', $node_type, $default_field);

    $element['field_name'] = [
      '#type' => 'select',
      '#title' => $this->t($label),
      '#options' => $this->getFieldOptions($node_type),
      '#ajax' => [
        'callback' => [$this, 'ajaxGetFieldPropertySelectionElement'],
        'wrapper' => 'component-field-mapping-properties-wrapper',
      ],
      '#default_value' => $default_field,
    ];

    if ($default_field_config && !$default_field_config->getFieldStorageDefinition()->isBaseField()) {
      $element['field_property'] = $this->getFieldPropertySelectionElement($default_field_config->getFieldStorageDefinition());;
    }
    else {
      // Base fields do not have properties, so set a value we can check for
      $element['field_property'] = [
        '#type' => 'hidden',
        '#value' => 'base',
      ];
    }

    return $element;
  }

  public function ajaxGetFieldPropertySelectionElement(array &$form, FormStateInterface $form_state) {
    return $form['add_components']['component_settings']['component_data'];
  }

  protected function getFieldPropertySelectionElement(FieldStorageConfigInterface $config) {
    $field_name = $config->getName();
    $properties = $config->getPropertyDefinitions();

    $property_options = [];
    foreach ($properties as $property => $definition) {
      $property_options[$property] = $definition->getLabel();
    }

    return [
      '#type' => 'select',
      '#title' => $this->t($field_name . ' Property'),
      '#options' => $property_options,
    ];
  }
}
